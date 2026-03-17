<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kode_tindakan_terapi', function (Blueprint $table) {
            $table->increments('idkode_tindakan_terapi');
            $table->string('kode')->nullable();
            $table->text('deskripsi_tindakan_terapi')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kode_tindakan_terapi');
    }
};
