<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangPenarikanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang_penarikan', function (Blueprint $table) {
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('penarikan_id')->constrained('penarikans')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('ket')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barang_penarikan');
    }
}
