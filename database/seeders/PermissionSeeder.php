<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create([
            'name' => 'Admin',
            'guard_name' => 'web'
        ]);

        $permissions = ['master-access', 'master-device-access', 'inventory-access', 'penjualan-access', 'management-access', 'report-access', 'setting-access'];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        $role->syncPermissions($permissions);
    }
}
