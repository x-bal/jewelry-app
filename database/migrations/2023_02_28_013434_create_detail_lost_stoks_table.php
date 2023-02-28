<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailLostStoksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_lost_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_stok_id')->constrained('lost_stoks')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('detail_lost_stoks');
    }
}
