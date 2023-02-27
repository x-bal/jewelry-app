<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailStokOpname extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function stokOpname()
    {
        return $this->belongsTo(StokOpname::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
