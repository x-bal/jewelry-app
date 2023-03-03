<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $devices = ['Device 1', 'Device 2'];

        foreach ($devices as $device) {
            Device::create([
                'nama_device' => $device
            ]);
        }
    }
}
