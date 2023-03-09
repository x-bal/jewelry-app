<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLostStoksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lost_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locator_id')->constrained('locators')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('tanggal');
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
        Schema::dropIfExists('lost_stoks');
    }
}
