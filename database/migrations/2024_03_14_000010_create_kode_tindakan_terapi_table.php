<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kode_tindakan_terapi', function (Blueprint $table) {
            $table->increments('idkode_tindakan_terapi');
            $table->bigInteger('idkategori');
            $table->bigInteger('idkategori_klinis');
            $table->string('kode', 50);
            $table->longText('deskripsi_tindakan_terapi');
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kode_tindakan_terapi');
    }
};
