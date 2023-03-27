<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function tipeBarang()
    {
        return $this->belongsTo(SubTipeBarang::class, 'sub_tipe_barang_id');
    }

    public function locator()
    {
        return $this->belongsTo(Locator::class);
    }
}
