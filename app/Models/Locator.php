<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locator extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}
