<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKodeTipeToTipeBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipe_barangs', function (Blueprint $table) {
            $table->string('kode')->nullable()->unique()->after('nama_tipe');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipe_barangs', function (Blueprint $table) {
            $table->dropColumn('kode');
        });
    }
}
