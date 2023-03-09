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
        Schema::create('barang_lost_stok', function (Blueprint $table) {
            $table->foreignId('lost_stok_id')->constrained('lost_stoks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('ket')->nullable();
            $table->integer('is_sync')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barang_lost_stok');
    }
}
