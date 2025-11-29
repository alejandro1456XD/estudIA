<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('name');
            $table->string('category');
            $table->string('language');
            $table->string('level');
            $table->text('description')->nullable();
            
            $table->string('type')->default('recorded');
            $table->json('schedule')->nullable();
            
            $table->boolean('is_published')->default(true);
            
            // --- NUEVO CAMPO: Estado de la clase en vivo ---
            $table->boolean('is_live_now')->default(false); 
            // ----------------------------------------------

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};