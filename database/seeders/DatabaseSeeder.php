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
        User::create([
            'username' => 'developer',
            'password' => bcrypt('secret'),
            'name' => 'Developer'
        ]);

        $this->call([
            LocatorSeeder::class,
            SatuanSeeder::class,
            TipeBarangSeeder::class,
            DeviceSeeder::class
        ]);
    }
}
