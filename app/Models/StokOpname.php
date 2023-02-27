<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function locator()
    {
        return $this->belongsTo(Locator::class);
    }

    public function details()
    {
        return $this->hasMany(DetailStokOpname::class);
    }
}
