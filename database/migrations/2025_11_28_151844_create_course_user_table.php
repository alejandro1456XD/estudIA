<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Esta tabla guarda la relación: ESTUDIANTE <-> CURSO
        Schema::create('course_user', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            
            // Opcional: Podríamos guardar el progreso aquí (0 a 100)
            $table->integer('progress')->default(0);
            
            $table->timestamps();

            // Evitar que alguien se inscriba dos veces al mismo curso
            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_user');
    }
};