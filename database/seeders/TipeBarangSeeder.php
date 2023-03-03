<?php

namespace Database\Seeders;

use App\Models\TipeBarang;
use Illuminate\Database\Seeder;

class TipeBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = ['Cincin', 'Gelang'];

        foreach ($types as $tipe) {
            TipeBarang::create([
                'nama_tipe' => $tipe
            ]);
        }
    }
}
