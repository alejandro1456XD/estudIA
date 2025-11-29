<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Crear tabla EVENTS
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            
            // Estas son las columnas que te faltaban
            $table->dateTime('start_time'); 
            $table->string('location')->nullable();
            $table->boolean('is_virtual')->default(false);
            $table->integer('max_attendees')->nullable();
            $table->string('type')->default('Evento'); 
            
            $table->timestamps();
        });

        // 2. Crear tabla ASISTENTES
        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Importante: 'events'
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_attendees');
        Schema::dropIfExists('events');
    }
};