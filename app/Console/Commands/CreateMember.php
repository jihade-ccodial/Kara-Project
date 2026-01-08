<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Organization;
use Illuminate\Console\Command;

class CreateMember extends Command
{
    protected $signature = 'member:create {organization_id} {email} {firstName} {lastName} {--active=1}';
    protected $description = 'Create a new member and assign to an organization.';

    public function handle()
    {
        $organizationId = $this->argument('organization_id');
        $email = $this->argument('email');
        $firstName = $this->argument('firstName');
        $lastName = $this->argument('lastName');
        $active = (bool) $this->option('active');

        $organization = Organization::find($organizationId);

        if (!$organization) {
            $this->error("Organization with ID {$organizationId} not found.");
            return 1;
        }

        // Check if member already exists for this organization
        $existingMember = Member::where('organization_id', $organization->id)
                                ->where('email', $email)
                                ->first();

        if ($existingMember) {
            $this->warn("Member with email {$email} already exists in organization {$organization->name}. Updating existing member.");
            $member = $existingMember;
        } else {
            $member = new Member();
        }

        $member->organization_id = $organization->id;
        $member->email = $email;
        $member->firstName = $firstName;
        $member->lastName = $lastName;
        $member->active = $active;
        // Set dummy hubspot_id and dates if not provided, or make them nullable in migration
        $member->hubspot_id = $member->hubspot_id ?? 'manual_' . uniqid();
        $member->hubspot_createdAt = $member->hubspot_createdAt ?? now();
        $member->hubspot_updatedAt = $member->hubspot_updatedAt ?? now();
        $member->save();

        $this->info("✓ Successfully created member:");
        $this->info("  ID: {$member->id}");
        $this->info("  Name: {$member->firstName} {$member->lastName}");
        $this->info("  Email: {$member->email}");
        $this->info("  Organization: {$organization->name} (ID: {$organization->id})");

        return 0;
    }
}

