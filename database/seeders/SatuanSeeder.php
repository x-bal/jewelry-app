<?php

namespace Database\Seeders;

use App\Models\Satuan;
use Illuminate\Database\Seeder;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $satuans = ['Gr', 'Pcs'];

        foreach ($satuans as $satuan) {
            Satuan::create([
                'nama_satuan' => $satuan
            ]);
        }
    }
}
