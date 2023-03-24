<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubTipeBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_tipe_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipe_barang_id')->constrained('tipe_barangs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('kode', 10);
            $table->string('nama', 35);
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
        Schema::dropIfExists('sub_tipe_barangs');
    }
}
