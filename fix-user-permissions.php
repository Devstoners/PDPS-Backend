<?php

/**
 * Fix User Permissions for Water Schemes
 */

echo "🔧 Fixing User Permissions for Water Schemes\n";
echo "===========================================\n\n";

echo "The 403 error indicates that the user is authenticated but lacks permissions.\n";
echo "Based on the debug info, the user (ID: 35, Name: Asanka) has admin role and all permissions.\n\n";

echo "🔍 Possible Issues:\n";
echo "==================\n";
echo "1. Permission middleware syntax issue (FIXED)\n";
echo "2. User permissions not properly cached\n";
echo "3. Role-permission relationship not properly established\n";
echo "4. Frontend authentication token issue\n\n";

echo "🛠️ Solutions to Try:\n";
echo "===================\n\n";

echo "Solution 1: Clear Permission Cache\n";
echo "-----------------------------------\n";
echo "Run these commands to clear permission cache:\n";
echo "```bash\n";
echo "php artisan cache:clear\n";
echo "php artisan config:clear\n";
echo "php artisan route:clear\n";
echo "php artisan permission:cache-reset\n";
echo "```\n\n";

echo "Solution 2: Re-assign User Role\n";
echo "-------------------------------\n";
echo "In Laravel tinker or create a command:\n";
echo "```php\n";
echo "// Get the user\n";
echo "\$user = User::find(35);\n";
echo "// Remove all roles\n";
echo "\$user->syncRoles([]);\n";
echo "// Re-assign admin role\n";
echo "\$user->assignRole('admin');\n";
echo "// Verify permissions\n";
echo "echo 'Can create: ' . (\$user->can('water-schemes.create') ? 'YES' : 'NO');\n";
echo "```\n\n";

echo "Solution 3: Check Frontend Authentication\n";
echo "----------------------------------------\n";
echo "Make sure the frontend is sending the correct headers:\n";
echo "```javascript\n";
echo "const token = localStorage.getItem('token'); // or however you store it\n";
echo "const headers = {\n";
echo "    'Authorization': `Bearer \${token}`,\n";
echo "    'Content-Type': 'application/json',\n";
echo "    'Accept': 'application/json'\n";
echo "};\n";
echo "```\n\n";

echo "Solution 4: Test with Different User\n";
echo "------------------------------------\n";
echo "Try logging in with a different admin user to see if the issue persists.\n\n";

echo "Solution 5: Manual Permission Assignment\n";
echo "---------------------------------------\n";
echo "If the above doesn't work, assign permissions directly:\n";
echo "```php\n";
echo "// Get the user\n";
echo "\$user = User::find(35);\n";
echo "// Get water scheme permissions\n";
echo "\$permissions = Permission::where('name', 'like', 'water-schemes%')->get();\n";
echo "// Assign permissions directly to user\n";
echo "\$user->givePermissionTo(\$permissions);\n";
echo "```\n\n";

echo "🧪 Test the Fix:\n";
echo "===============\n";
echo "1. Clear all caches\n";
echo "2. Re-assign user role\n";
echo "3. Test creating a water scheme again\n";
echo "4. Check Laravel logs for any new errors\n\n";

echo "✅ All solutions provided!\n";
echo "Try them in order until the issue is resolved.\n";

