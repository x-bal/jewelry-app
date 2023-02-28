<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailLostStok extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function lostStok()
    {
        return $this->belongsTo(LostStok::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
