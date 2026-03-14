<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perawat', function (Blueprint $table) {
            $table->increments('idperawat');
            $table->bigInteger('iduser');
            $table->longText('alamat');
            $table->string('no_hp', 45);
            $table->string('pendidikan', 100);
            $table->char('jenis_kelamin', 1);
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perawat');
    }
};
