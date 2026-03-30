<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->increments('idrekam_medis');
            $table->timestamp('created_at')->nullable();
            $table->text('anamnesa')->nullable();
            $table->text('temuan_klinis')->nullable();
            $table->text('diagnosa')->nullable();
            $table->integer('dokter_pemeriksa')->nullable();
            $table->integer('idreservasi_dokter')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekam_medis');
    }
};
