<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\DummyBarang;
use App\Models\Locator;
use App\Models\TipeBarang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $tag = Barang::where('rfid', $row['tag'])->first();
        $tipe = TipeBarang::where('kode', $row['tipe_barang'])->first();
        $locator = Locator::where('nama_locator', $row['nama_locator'])->first();

        if (!$tag && $tipe && $locator) {
            return new Barang([
                'nama_barang'  => $row['nama_barang'],
                'kode_barang'  => $row['kode_barang'],
                'rfid' => $row['tag'],
                'harga'  => $row['harga'],
                'berat'  => $row['berat'],
                'satuan'  => 'Gram',
                'tipe_barang_id'  => TipeBarang::where('kode', $row['tipe_barang'])->first()->id,
                'locator_id'  => Locator::where('nama_locator', $row['nama_locator'])->first()->id,
            ]);
        } else {
            return new DummyBarang([
                'nama_barang'  => $row['nama_barang'],
                'kode_barang'  => $row['kode_barang'],
                'rfid' => $row['tag'],
                'harga'  => $row['harga'],
                'berat'  => $row['berat'],
                'satuan'  => 'Gram',
                'tipe_barang_id'  => $row['tipe_barang'],
                'locator_id'  => $row['nama_locator'],
            ]);
        }
    }
}
