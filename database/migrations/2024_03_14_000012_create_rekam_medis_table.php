<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->increments('idrekam_medis');
            $table->bigInteger('idreservasi_dokter');
            $table->bigInteger('dokter_pemeriksa');
            $table->longText('anamnesa');
            $table->longText('temuan_klinis');
            $table->longText('diagnosa');
            $table->timestamp('created_at');
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
