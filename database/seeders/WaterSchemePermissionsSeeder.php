<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class WaterSchemePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create water scheme permissions
        $permissions = [
            'water-schemes.read',
            'water-schemes.create',
            'water-schemes.update',
            'water-schemes.delete'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        // Assign all permissions to officer water bill role
        $officerWaterBillRole = Role::firstOrCreate(['name' => 'officerwaterbill']);
        $officerWaterBillRole->givePermissionTo($permissions);

        // Assign read permission to regular officer role
        $officerRole = Role::firstOrCreate(['name' => 'officer']);
        $officerRole->givePermissionTo(['water-schemes.read']);

        $this->command->info('Water scheme permissions created and assigned to roles.');
    }
}
