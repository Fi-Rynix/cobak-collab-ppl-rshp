<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokter', function (Blueprint $table) {
            $table->increments('iddokter');
            $table->bigInteger('iduser');
            $table->longText('alamat');
            $table->string('no_hp', 45);
            $table->string('bidang_dokter', 100);
            $table->char('jenis_kelamin', 1);
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokter');
    }
};
