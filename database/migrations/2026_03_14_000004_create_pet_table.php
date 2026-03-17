<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pet', function (Blueprint $table) {
            $table->increments('idpet');
            $table->string('nama')->nullable();
            $table->timestamp('tanggal_lahir')->nullable();
            $table->string('warna_tanda')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->unsignedInteger('idpemilik');
            $table->unsignedInteger('idras_hewan')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pet');
    }
};
