<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabla de RECURSOS
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que sube el recurso
            $table->string('title');
            $table->text('description')->nullable();
            
            // Información del Archivo
            $table->string('file_path');
            $table->string('file_type')->default('file'); // pdf, video, image, zip
            $table->string('file_size')->nullable(); // Ej: "2.5 MB"
            
            // Organización
            $table->string('category'); // Programación, Diseño, Matemáticas...
            
            // Estadísticas (para saber cuáles son populares)
            $table->integer('downloads')->default(0);
            
            $table->timestamps();
        });

        // 2. Tabla de CALIFICACIONES (Para el ranking de mejores recursos)
        Schema::create('resource_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rating'); // Valor de 1 a 5 estrellas
            $table->timestamps();
            
            // Regla: Un usuario solo puede calificar una vez el mismo recurso
            $table->unique(['resource_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('resource_ratings');
        Schema::dropIfExists('resources');
    }
};