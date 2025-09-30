<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "🔍 Debugging User Permissions for Water Schemes\n";
echo "==============================================\n\n";

// Check if we can connect to the database
try {
    $users = DB::table('users')->count();
    echo "✅ Database connection: OK ($users users found)\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

echo "\n📋 Checking Water Scheme Permissions:\n";
echo "=====================================\n";

// Check if water scheme permissions exist
$waterSchemePermissions = [
    'water-schemes.read',
    'water-schemes.create', 
    'water-schemes.update',
    'water-schemes.delete'
];

foreach ($waterSchemePermissions as $permission) {
    $exists = Permission::where('name', $permission)->exists();
    echo ($exists ? "✅" : "❌") . " Permission '$permission': " . ($exists ? "EXISTS" : "MISSING") . "\n";
}

echo "\n👥 Checking Roles and Their Permissions:\n";
echo "=======================================\n";

$roles = ['admin', 'officerwaterbill', 'officer'];

foreach ($roles as $roleName) {
    $role = Role::where('name', $roleName)->first();
    
    if ($role) {
        echo "✅ Role '$roleName': EXISTS\n";
        $permissions = $role->permissions->pluck('name')->toArray();
        $waterSchemePerms = array_filter($permissions, function($perm) {
            return strpos($perm, 'water-schemes') === 0;
        });
        
        if (!empty($waterSchemePerms)) {
            echo "   📋 Water Scheme Permissions: " . implode(', ', $waterSchemePerms) . "\n";
        } else {
            echo "   ❌ No water scheme permissions assigned\n";
        }
    } else {
        echo "❌ Role '$roleName': MISSING\n";
    }
}

echo "\n👤 Checking Sample Users:\n";
echo "==========================\n";

// Get a few sample users to check their roles
$sampleUsers = DB::table('users')->limit(5)->get();

foreach ($sampleUsers as $user) {
    echo "User ID: {$user->id}, Name: {$user->name}\n";
    
    // Get user roles
    $userRoles = DB::table('model_has_roles')
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->where('model_has_roles.model_id', $user->id)
        ->pluck('roles.name');
    
    if ($userRoles->isNotEmpty()) {
        echo "   Roles: " . $userRoles->implode(', ') . "\n";
        
        // Check if user has water scheme permissions
        $hasWaterSchemePerms = DB::table('model_has_permissions')
            ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_has_permissions.model_id', $user->id)
            ->where('permissions.name', 'like', 'water-schemes%')
            ->exists();
            
        echo "   Water Scheme Permissions: " . ($hasWaterSchemePerms ? "✅ YES" : "❌ NO") . "\n";
    } else {
        echo "   ❌ No roles assigned\n";
    }
    echo "\n";
}

echo "🔧 Recommendations:\n";
echo "===================\n";

// Check if permissions need to be created
$missingPermissions = [];
foreach ($waterSchemePermissions as $permission) {
    if (!Permission::where('name', $permission)->exists()) {
        $missingPermissions[] = $permission;
    }
}

if (!empty($missingPermissions)) {
    echo "❌ Missing permissions: " . implode(', ', $missingPermissions) . "\n";
    echo "   Run: php artisan db:seed --class=WaterSchemePermissionsSeeder\n";
}

// Check if roles need permissions
$adminRole = Role::where('name', 'admin')->first();
$officerWaterBillRole = Role::where('name', 'officerwaterbill')->first();

if ($adminRole) {
    $adminWaterPerms = $adminRole->permissions()->where('name', 'like', 'water-schemes%')->count();
    if ($adminWaterPerms < 4) {
        echo "❌ Admin role missing water scheme permissions\n";
        echo "   Run: php artisan db:seed --class=WaterSchemePermissionsSeeder\n";
    }
}

if ($officerWaterBillRole) {
    $officerWaterPerms = $officerWaterBillRole->permissions()->where('name', 'like', 'water-schemes%')->count();
    if ($officerWaterPerms < 4) {
        echo "❌ OfficerWaterBill role missing water scheme permissions\n";
        echo "   Run: php artisan db:seed --class=WaterSchemePermissionsSeeder\n";
    }
}

echo "\n✅ Debug complete! Check the results above.\n";

