<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $dev = User::create([
            'username' => 'developer',
            'password' => bcrypt('secret'),
            'name' => 'Developer'
        ]);

        $this->call([
            LocatorSeeder::class,
            TipeBarangSeeder::class,
            DeviceSeeder::class,
            PermissionSeeder::class,
            SettingSeeder::class
        ]);

        $dev->assignRole(1);
    }
}
