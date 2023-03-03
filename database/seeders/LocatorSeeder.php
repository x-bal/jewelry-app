<?php

namespace Database\Seeders;

use App\Models\Locator;
use Illuminate\Database\Seeder;

class LocatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locators = ['Locator 1', 'Locator 2'];

        foreach ($locators as $locator) {
            Locator::create([
                'nama_locator' => $locator
            ]);
        }
    }
}
