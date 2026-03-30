<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('temu_dokter', function (Blueprint $table) {
            $table->increments('idreservasi_dokter');
            $table->integer('no_urut')->nullable();
            $table->timestamp('waktu_daftar')->nullable();
            $table->string('status', 1)->default('');
            $table->unsignedInteger('idpet');
            $table->integer('idrole_user');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('temu_dokter');
    }
};
