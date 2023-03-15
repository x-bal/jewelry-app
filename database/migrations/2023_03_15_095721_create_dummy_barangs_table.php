<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDummyBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dummy_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('tipe_barang_id');
            $table->string('locator_id');
            $table->string('rfid')->nullable();
            $table->string('nama_barang');
            $table->string('kode_barang');
            $table->float('berat');
            $table->string('satuan')->default('Gram');
            $table->integer('harga');
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
        Schema::dropIfExists('dummy_barangs');
    }
}
