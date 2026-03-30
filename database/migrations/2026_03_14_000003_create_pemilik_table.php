<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pemilik', function (Blueprint $table) {
            $table->increments('idpemilik');
            $table->string('no_wa')->nullable();
            $table->string('alamat')->nullable();
            $table->unsignedBigInteger('iduser');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pemilik');
    }
};
