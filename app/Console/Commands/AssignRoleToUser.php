<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class AssignRoleToUser extends Command
{
    protected $signature = 'tenant:assign-role {email} {role}';
    protected $description = 'Assign a role to a user across all tenant databases where they exist';

    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        $schools = School::all();

        foreach ($schools as $school) {
            try {
                // Set tenant database
                config(['database.connections.tenant.database' => $school->database]);
                DB::purge('tenant');
                DB::reconnect('tenant');

                $this->info("Checking {$school->name} ({$school->database})...");

                // Find user
                $user = DB::connection('tenant')->table('users')->where('email', $email)->first();

                if (!$user) {
                    $this->warn("  User not found in this school");
                    continue;
                }

                // Find role
                $role = DB::connection('tenant')->table('roles')->where('name', $roleName)->first();

                if (!$role) {
                    $this->error("  Role '{$roleName}' not found. Available roles:");
                    $availableRoles = DB::connection('tenant')->table('roles')->pluck('name')->toArray();
                    $this->info("  " . implode(', ', $availableRoles));
                    continue;
                }

                // Check if user already has the role
                $hasRole = DB::connection('tenant')->table('model_has_roles')
                    ->where('model_type', 'App\\Models\\User')
                    ->where('model_id', $user->id)
                    ->where('role_id', $role->id)
                    ->exists();

                if ($hasRole) {
                    $this->info("  ✓ User already has role '{$roleName}'");
                } else {
                    DB::connection('tenant')->table('model_has_roles')->insert([
                        'role_id' => $role->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $user->id,
                    ]);
                    $this->info("  ✓ Assigned role '{$roleName}' to {$email}");
                }

                // Show all user roles
                $userRoleIds = DB::connection('tenant')->table('model_has_roles')
                    ->where('model_type', 'App\\Models\\User')
                    ->where('model_id', $user->id)
                    ->pluck('role_id')->toArray();

                $userRoleNames = DB::connection('tenant')->table('roles')
                    ->whereIn('id', $userRoleIds)
                    ->pluck('name')->toArray();

                $this->info("  Current roles: " . implode(', ', $userRoleNames));

            } catch (\Exception $e) {
                $this->error("  Error: " . $e->getMessage());
            }
        }

        return 0;
    }
}
