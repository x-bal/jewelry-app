<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostStok extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function locator()
    {
        return $this->belongsTo(Locator::class);
    }

    public function barangs()
    {
        return $this->belongsToMany(Barang::class);
    }
}
