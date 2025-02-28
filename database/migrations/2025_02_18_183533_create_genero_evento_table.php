<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('genero_evento', function (Blueprint $table) {
            // INT firmado, para coincidir con `id` int(11) en `evento`
            $table->integer('evento_id');
            $table->integer('genero_id');
            
            // Clave primaria compuesta
            $table->primary(['evento_id','genero_id']);

            // Índices (puedes conservar los nombres si lo deseas)
            $table->index('evento_id', 'IDX_D2E992A87A5F842');
            $table->index('genero_id', 'IDX_D2E992ABCE7B795');

            // Llaves foráneas con onDelete('cascade')
            $table->foreign('evento_id')
                  ->references('id')
                  ->on('evento')
                  ->onDelete('cascade');

            $table->foreign('genero_id')
                  ->references('id')
                  ->on('generos')
                  ->onDelete('cascade');

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down()
    {
        Schema::dropIfExists('genero_evento');
    }
};
