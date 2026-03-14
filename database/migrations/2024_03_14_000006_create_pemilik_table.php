<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemilik', function (Blueprint $table) {
            $table->increments('idpemilik');
            $table->bigInteger('iduser');
            $table->string('no_wa', 15);
            $table->longText('alamat');
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemilik');
    }
};
