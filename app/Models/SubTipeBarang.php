<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTipeBarang extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function tipe()
    {
        return $this->belongsTo(TipeBarang::class, 'tipe_barang_id');
    }
}
