<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penarikan extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function barangs()
    {
        return $this->belongsToMany(Barang::class)->withPivot('ket');
    }

    public function locator()
    {
        return $this->belongsTo(Locator::class);
    }
}
