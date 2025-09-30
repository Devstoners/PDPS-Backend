<?php

/**
 * Fix Water Scheme Permissions
 */

echo "🔧 Fixing Water Scheme Permissions\n";
echo "==================================\n\n";

echo "The 403 error indicates that:\n";
echo "✅ User is authenticated (otherwise it would be 401)\n";
echo "❌ User doesn't have the required permissions\n\n";

echo "🔍 Possible Issues:\n";
echo "==================\n";
echo "1. User doesn't have the correct role assigned\n";
echo "2. User's role doesn't have the required permissions\n";
echo "3. User is assigned to the wrong role\n\n";

echo "🛠️ Solutions:\n";
echo "=============\n\n";

echo "Solution 1: Check Current User Role\n";
echo "----------------------------------\n";
echo "Run this in Laravel tinker or create a test endpoint:\n";
echo "```php\n";
echo "// Get the user who's getting the 403 error\n";
echo "\$user = User::find(\$userId);\n";
echo "echo 'User: ' . \$user->name . PHP_EOL;\n";
echo "echo 'Roles: ' . \$user->roles->pluck('name')->implode(', ') . PHP_EOL;\n";
echo "echo 'Can create water schemes: ' . (\$user->can('water-schemes.create') ? 'YES' : 'NO') . PHP_EOL;\n";
echo "```\n\n";

echo "Solution 2: Assign Correct Role\n";
echo "-------------------------------\n";
echo "If the user doesn't have admin or officerwaterbill role:\n";
echo "```php\n";
echo "// Assign admin role (has all permissions)\n";
echo "\$user = User::find(\$userId);\n";
echo "\$user->assignRole('admin');\n";
echo "// OR assign officerwaterbill role (has water scheme permissions)\n";
echo "\$user->assignRole('officerwaterbill');\n";
echo "```\n\n";

echo "Solution 3: Re-run Permission Seeder\n";
echo "-----------------------------------\n";
echo "Make sure permissions are properly assigned:\n";
echo "```bash\n";
echo "php artisan db:seed --class=WaterSchemePermissionsSeeder\n";
echo "```\n\n";

echo "Solution 4: Manual Permission Assignment\n";
echo "---------------------------------------\n";
echo "If the seeder doesn't work, assign permissions manually:\n";
echo "```php\n";
echo "// Get the role\n";
echo "\$role = Role::where('name', 'admin')->first();\n";
echo "// Get the permissions\n";
echo "\$permissions = Permission::where('name', 'like', 'water-schemes%')->get();\n";
echo "// Assign permissions to role\n";
echo "\$role->givePermissionTo(\$permissions);\n";
echo "```\n\n";

echo "Solution 5: Check Frontend Authentication\n";
echo "-----------------------------------------\n";
echo "Make sure the frontend is sending the correct headers:\n";
echo "```javascript\n";
echo "// Check if Authorization header is being sent\n";
echo "const token = localStorage.getItem('token'); // or however you store it\n";
echo "const headers = {\n";
echo "    'Authorization': `Bearer \${token}`,\n";
echo "    'Content-Type': 'application/json',\n";
echo "    'Accept': 'application/json'\n";
echo "};\n";
echo "```\n\n";

echo "🧪 Test the Fix\n";
echo "===============\n";
echo "1. Use the debug endpoint: GET /api/water-schemes?debug=1\n";
echo "2. Check the response to see user's current permissions\n";
echo "3. If permissions are missing, apply one of the solutions above\n";
echo "4. Test creating a water scheme again\n\n";

echo "✅ All solutions provided!\n";
echo "Choose the appropriate solution based on your situation.\n";

