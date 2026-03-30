<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detail_rekam_medis', function (Blueprint $table) {
            $table->increments('iddetail_rekam_medis');
            $table->integer('idrekam_medis');
            $table->integer('idkode_tindakan_terapi');
            $table->text('detail')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_rekam_medis');
    }
};
