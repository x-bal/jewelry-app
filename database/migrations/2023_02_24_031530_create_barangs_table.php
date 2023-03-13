<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipe_barang_id')->constrained('tipe_barangs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('locator_id')->constrained('locators')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('rfid')->unique('rfid')->nullable();
            $table->string('nama_barang');
            $table->float('berat');
            $table->string('satuan')->default('Gram');
            $table->integer('harga');
            $table->enum('status', ['Tersedia', 'Terjual', 'Loss', 'Ditarik'])->default('Tersedia');
            $table->string('old_rfid')->nullable();
            $table->integer('is_sync')->default(0);
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
        Schema::dropIfExists('barangs');
    }
}
