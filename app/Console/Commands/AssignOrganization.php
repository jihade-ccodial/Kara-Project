<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Console\Command;

class AssignOrganization extends Command
{
    protected $signature = 'user:assign-organization {user_identifier} {organization_identifier}';
    protected $description = 'Assigns a user to an organization.';

    public function handle()
    {
        $userIdentifier = $this->argument('user_identifier');
        $organizationIdentifier = $this->argument('organization_identifier');

        // Find the user
        $user = User::where('id', $userIdentifier)
                    ->orWhere('email', $userIdentifier)
                    ->first();

        if (!$user) {
            $this->error("User '{$userIdentifier}' not found.");
            return 1;
        }

        // Find the organization
        $organization = Organization::where('id', $organizationIdentifier)
                                    ->orWhere('name', $organizationIdentifier)
                                    ->orWhere('hubspot_portalId', $organizationIdentifier)
                                    ->first();

        if (!$organization) {
            $this->error("Organization '{$organizationIdentifier}' not found.");
            return 1;
        }

        // Check if already assigned
        if ($user->organizations->contains($organization->id)) {
            $this->info("User {$user->email} is already assigned to organization {$organization->name}");
            return 0;
        }

        // Attach the user to the organization
        $user->organizations()->attach($organization->id);

        $this->info("Successfully assigned user {$user->email} to organization {$organization->name} (ID: {$organization->id})");

        return 0;
    }
}

