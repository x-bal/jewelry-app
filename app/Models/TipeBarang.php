<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeBarang extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function subs()
    {
        return $this->hasMany(SubTipeBarang::class);
    }
}
