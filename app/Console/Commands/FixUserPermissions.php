<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixUserPermissions extends Command
{
    protected $signature = 'fix:user-permissions {user_id=35}';
    protected $description = 'Fix user permissions for water schemes';

    public function handle()
    {
        $userId = $this->argument('user_id');
        
        $this->info("🔧 Fixing permissions for user ID: $userId");
        
        try {
            $user = User::find($userId);
            
            if (!$user) {
                $this->error("❌ User not found with ID: $userId");
                return;
            }
            
            $this->info("👤 User: {$user->name} (ID: {$user->id})");
            
            // Show current roles
            $currentRoles = $user->roles->pluck('name')->toArray();
            $this->info("📋 Current roles: " . implode(', ', $currentRoles));
            
            // Show current permissions
            $currentPermissions = $user->getAllPermissions()->pluck('name')->toArray();
            $waterSchemePerms = array_filter($currentPermissions, function($perm) {
                return strpos($perm, 'water-schemes') === 0;
            });
            $this->info("🔑 Water scheme permissions: " . implode(', ', $waterSchemePerms));
            
            // Test current permissions
            $canCreate = $user->can('water-schemes.create');
            $this->info("✅ Can create water schemes: " . ($canCreate ? 'YES' : 'NO'));
            
            if (!$canCreate) {
                $this->warn("⚠️ User cannot create water schemes. Fixing...");
                
                // Remove all roles
                $user->syncRoles([]);
                $this->info("🗑️ Removed all roles");
                
                // Re-assign admin role
                $user->assignRole('admin');
                $this->info("👑 Assigned admin role");
                
                // Test permissions again
                $canCreateAfter = $user->can('water-schemes.create');
                $this->info("✅ Can create water schemes after fix: " . ($canCreateAfter ? 'YES' : 'NO'));
                
                if (!$canCreateAfter) {
                    $this->error("❌ Still cannot create. Trying direct permission assignment...");
                    
                    // Get water scheme permissions
                    $permissions = Permission::where('name', 'like', 'water-schemes%')->get();
                    if ($permissions->count() > 0) {
                        $user->givePermissionTo($permissions);
                        $this->info("🔑 Assigned water scheme permissions directly");
                        
                        // Test again
                        $canCreateFinal = $user->can('water-schemes.create');
                        $this->info("✅ Can create water schemes after direct assignment: " . ($canCreateFinal ? 'YES' : 'NO'));
                    } else {
                        $this->error("❌ No water scheme permissions found in database");
                    }
                }
            } else {
                $this->info("✅ User already has correct permissions");
            }
            
            $this->newLine();
            $this->info("🎯 Final Status:");
            $this->info("User: {$user->name}");
            $this->info("Roles: " . $user->roles->pluck('name')->implode(', '));
            $this->info("Can create: " . ($user->can('water-schemes.create') ? 'YES' : 'NO'));
            $this->info("Can read: " . ($user->can('water-schemes.read') ? 'YES' : 'NO'));
            $this->info("Can update: " . ($user->can('water-schemes.update') ? 'YES' : 'NO'));
            $this->info("Can delete: " . ($user->can('water-schemes.delete') ? 'YES' : 'NO'));
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }
}

