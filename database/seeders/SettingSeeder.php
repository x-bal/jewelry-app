<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'name' => 'title',
            'val' => 'Jewelry'
        ]);

        Setting::create([
            'name' => 'tagline',
            'val' => 'Toko Perhiasan Hade Putra Ciwidey'
        ]);

        Setting::create([
            'name' => 'url',
            'val' => 'http://127.0.0.1:8002'
        ]);

        Setting::create([
            'name' => 'bg',
            'val' => 'background/login-bg.jpg'
        ]);
    }
}
