<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temu_dokter', function (Blueprint $table) {
            $table->increments('idreservasi_dokter');
            $table->bigInteger('idpet');
            $table->bigInteger('idrole_user');
            $table->integer('no_urut')->nullable();
            $table->string('status', 50)->default('menunggu');
            $table->timestamp('waktu_daftar');
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temu_dokter');
    }
};
