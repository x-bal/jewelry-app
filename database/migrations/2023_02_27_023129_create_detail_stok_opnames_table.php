<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailStokOpnamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_stok_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_opname_id')->constrained('stok_opnames')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_stok_opnames');
    }
}
