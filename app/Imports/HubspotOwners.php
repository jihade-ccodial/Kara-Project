<?php

namespace App\Imports;

use App\Helpers\HubspotClientHelper;
use App\Models\Member;
use HubSpot\Client\Crm\Owners\ApiException;
use Illuminate\Support\Carbon;

class HubspotOwners
{
    public static function sync_with_hubspot($hubspot, $user, $organization_id)
    {
        if (! $hubspot) {
            $hubspot = HubspotClientHelper::createFactory($user);
        }
        try {
            $after = null;
            $owners_ids = [];
            do {
                $apiResponse = $hubspot->crm()->owners()->ownersApi()->getPage(null, $after, 100, false);
                $owners = $apiResponse['results'];

                foreach ($owners as $owner) {
                    try {
                        $ownerEmail = $owner->getEmail();
                        $ownerHubspotId = $owner->getId();
                        
                        // First, try to find by hubspot_id
                        $owner_db_record = Member::where('organization_id', $organization_id)
                                                 ->where('hubspot_id', $ownerHubspotId)
                                                 ->first();
                        
                        // If not found, try to find by email (might be a manually created member)
                        if (!$owner_db_record && $ownerEmail) {
                            $owner_db_record = Member::where('organization_id', $organization_id)
                                                     ->where('email', $ownerEmail)
                                                     ->first();
                        }
                        
                        // Update or create the member
                        if ($owner_db_record) {
                            $owner_db_record->update([
                                'hubspot_id' => $ownerHubspotId,
                                'email' => $ownerEmail,
                                'firstName' => $owner->getFirstName(),
                                'lastName' => $owner->getLastName(),
                                'hubspot_createdAt' => Carbon::parse($owner->getCreatedAt())->toDateTimeString(),
                                'hubspot_updatedAt' => Carbon::parse($owner->getUpdatedAt())->toDateTimeString(),
                                'hubspot_archived' => false,
                            ]);
                        } else {
                            $owner_db_record = Member::create([
                                'organization_id' => $organization_id,
                                'hubspot_id' => $ownerHubspotId,
                                'email' => $ownerEmail,
                                'firstName' => $owner->getFirstName(),
                                'lastName' => $owner->getLastName(),
                                'hubspot_createdAt' => Carbon::parse($owner->getCreatedAt())->toDateTimeString(),
                                'hubspot_updatedAt' => Carbon::parse($owner->getUpdatedAt())->toDateTimeString(),
                                'hubspot_archived' => false,
                            ]);
                        }
                        $owners_ids[] = $owner_db_record->id;
                    } catch (\Exception $e) {
                        \Log::warning('Failed to sync HubSpot owner: ' . $e->getMessage(), [
                            'owner_id' => $owner->getId(),
                            'owner_email' => $owner->getEmail(),
                            'organization_id' => $organization_id,
                            'exception' => $e->getMessage()
                        ]);
                    }
                }

                if (isset($apiResponse['paging'])) {
                    $paging = $apiResponse['paging'];
                    $after = $paging->getNext()['after'];
                } else {
                    $after = null;
                }

            } while (! empty($after));

            //archived
            do {
                $apiResponse = $hubspot->crm()->owners()->ownersApi()->getPage(null, $after, 100, true);
                $owners = $apiResponse['results'];

                foreach ($owners as $owner) {
                    try {
                        $ownerEmail = $owner->getEmail();
                        $ownerHubspotId = $owner->getId();

                        $owner_db_record = Member::where('organization_id', $organization_id)
                                                 ->where('hubspot_id', $ownerHubspotId)
                                                 ->first();
                        
                        if (!$owner_db_record && $ownerEmail) {
                            $owner_db_record = Member::where('organization_id', $organization_id)
                                                     ->where('email', $ownerEmail)
                                                     ->first();
                        }

                        if ($owner_db_record) {
                            $owner_db_record->update([
                                'hubspot_id' => $ownerHubspotId,
                                'email' => $ownerEmail,
                                'firstName' => $owner->getFirstName(),
                                'lastName' => $owner->getLastName(),
                                'hubspot_createdAt' => Carbon::parse($owner->getCreatedAt())->toDateTimeString(),
                                'hubspot_updatedAt' => Carbon::parse($owner->getUpdatedAt())->toDateTimeString(),
                                'hubspot_archived' => true,
                            ]);
                        } else {
                            $owner_db_record = Member::create([
                                'organization_id' => $organization_id,
                                'hubspot_id' => $ownerHubspotId,
                                'email' => $ownerEmail,
                                'firstName' => $owner->getFirstName(),
                                'lastName' => $owner->getLastName(),
                                'hubspot_createdAt' => Carbon::parse($owner->getCreatedAt())->toDateTimeString(),
                                'hubspot_updatedAt' => Carbon::parse($owner->getUpdatedAt())->toDateTimeString(),
                                'hubspot_archived' => true,
                            ]);
                        }
                        $owners_ids[] = $owner_db_record->id;
                    } catch (\Exception $e) {
                        \Log::warning('Failed to sync archived HubSpot owner: ' . $e->getMessage(), [
                            'owner_id' => $owner->getId(),
                            'owner_email' => $owner->getEmail(),
                            'organization_id' => $organization_id,
                            'exception' => $e->getMessage()
                        ]);
                    }
                }

                if (isset($apiResponse['paging'])) {
                    $paging = $apiResponse['paging'];
                    $after = $paging->getNext()['after'];
                } else {
                    $after = null;
                }

            } while (! empty($after));

            Member::where('organization_id', $organization_id)->whereNotIn('id', $owners_ids)->delete();
        } catch (ApiException $e) {
            \Log::error('Exception when calling owners_api->get_page: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            // Re-throw to be caught by the controller
            throw $e;
        } catch (\Exception $e) {
            \Log::error('General Exception in HubspotOwners sync: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            throw $e;
        }
    }
}
