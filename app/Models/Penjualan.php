<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function barangs()
    {
        return $this->belongsToMany(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
