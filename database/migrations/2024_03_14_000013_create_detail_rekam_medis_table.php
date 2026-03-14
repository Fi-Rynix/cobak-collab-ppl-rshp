<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_rekam_medis', function (Blueprint $table) {
            $table->increments('iddetail_rekam_medis');
            $table->bigInteger('idrekam_medis');
            $table->bigInteger('idkode_tindakan_terapi');
            $table->longText('detail')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_rekam_medis');
    }
};
