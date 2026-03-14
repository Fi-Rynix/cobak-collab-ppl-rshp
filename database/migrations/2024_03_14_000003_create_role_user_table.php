<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->increments('idrole_user');
            $table->bigInteger('iduser');
            $table->unsignedInteger('idrole');
            $table->boolean('status')->default(true);
            $table->foreign('idrole')->references('idrole')->on('role')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
