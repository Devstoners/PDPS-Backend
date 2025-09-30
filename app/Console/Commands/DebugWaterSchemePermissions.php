<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class DebugWaterSchemePermissions extends Command
{
    protected $signature = 'debug:water-scheme-permissions';
    protected $description = 'Debug water scheme permissions and user roles';

    public function handle()
    {
        $this->info('🔍 Debugging Water Scheme Permissions');
        $this->line('==============================================');
        $this->newLine();

        // Check if water scheme permissions exist
        $this->info('📋 Checking Water Scheme Permissions:');
        $this->line('=====================================');

        $waterSchemePermissions = [
            'water-schemes.read',
            'water-schemes.create', 
            'water-schemes.update',
            'water-schemes.delete'
        ];

        foreach ($waterSchemePermissions as $permission) {
            $exists = Permission::where('name', $permission)->exists();
            $status = $exists ? '✅' : '❌';
            $this->line("$status Permission '$permission': " . ($exists ? "EXISTS" : "MISSING"));
        }

        $this->newLine();
        $this->info('👥 Checking Roles and Their Permissions:');
        $this->line('=======================================');

        $roles = ['admin', 'officerwaterbill', 'officer'];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            
            if ($role) {
                $this->line("✅ Role '$roleName': EXISTS");
                $permissions = $role->permissions->pluck('name')->toArray();
                $waterSchemePerms = array_filter($permissions, function($perm) {
                    return strpos($perm, 'water-schemes') === 0;
                });
                
                if (!empty($waterSchemePerms)) {
                    $this->line("   📋 Water Scheme Permissions: " . implode(', ', $waterSchemePerms));
                } else {
                    $this->line("   ❌ No water scheme permissions assigned");
                }
            } else {
                $this->line("❌ Role '$roleName': MISSING");
            }
        }

        $this->newLine();
        $this->info('👤 Checking Sample Users:');
        $this->line('==========================');

        // Get a few sample users to check their roles
        $sampleUsers = User::with('roles')->limit(5)->get();

        foreach ($sampleUsers as $user) {
            $this->line("User ID: {$user->id}, Name: {$user->name}");
            
            if ($user->roles->isNotEmpty()) {
                $roleNames = $user->roles->pluck('name')->toArray();
                $this->line("   Roles: " . implode(', ', $roleNames));
                
                // Check if user has water scheme permissions
                $hasWaterSchemePerms = $user->can('water-schemes.create');
                $this->line("   Can create water schemes: " . ($hasWaterSchemePerms ? "✅ YES" : "❌ NO"));
            } else {
                $this->line("   ❌ No roles assigned");
            }
            $this->newLine();
        }

        $this->info('🔧 Recommendations:');
        $this->line('===================');

        // Check if permissions need to be created
        $missingPermissions = [];
        foreach ($waterSchemePermissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                $missingPermissions[] = $permission;
            }
        }

        if (!empty($missingPermissions)) {
            $this->error("❌ Missing permissions: " . implode(', ', $missingPermissions));
            $this->line("   Run: php artisan db:seed --class=WaterSchemePermissionsSeeder");
        }

        // Check if roles need permissions
        $adminRole = Role::where('name', 'admin')->first();
        $officerWaterBillRole = Role::where('name', 'officerwaterbill')->first();

        if ($adminRole) {
            $adminWaterPerms = $adminRole->permissions()->where('name', 'like', 'water-schemes%')->count();
            if ($adminWaterPerms < 4) {
                $this->error("❌ Admin role missing water scheme permissions");
                $this->line("   Run: php artisan db:seed --class=WaterSchemePermissionsSeeder");
            }
        }

        if ($officerWaterBillRole) {
            $officerWaterPerms = $officerWaterBillRole->permissions()->where('name', 'like', 'water-schemes%')->count();
            if ($officerWaterPerms < 4) {
                $this->error("❌ OfficerWaterBill role missing water scheme permissions");
                $this->line("   Run: php artisan db:seed --class=WaterSchemePermissionsSeeder");
            }
        }

        $this->newLine();
        $this->info('✅ Debug complete! Check the results above.');
    }
}

