<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet', function (Blueprint $table) {
            $table->increments('idpet');
            $table->bigInteger('idpemilik');
            $table->bigInteger('idras_hewan');
            $table->string('nama', 100);
            $table->date('tanggal_lahir');
            $table->string('warna_tanda', 45);
            $table->char('jenis_kelamin', 1);
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet');
    }
};
