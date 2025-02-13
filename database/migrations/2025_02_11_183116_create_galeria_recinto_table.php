<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('galeria_recinto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recinto')->nullable(false);
            $table->string('image', 190)->collation('utf8mb4_unicode_ci');
            $table->foreign('recinto')->references('id')->on('recinto')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('galeria_recinto');
    }
};

