<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ras_hewan', function (Blueprint $table) {
            $table->increments('idras_hewan');
            $table->string('nama_ras')->nullable();
            $table->unsignedInteger('idjenis_hewan')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ras_hewan');
    }
};
