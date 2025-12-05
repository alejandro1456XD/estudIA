<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('title');
            $table->text('description')->nullable();
            
            // Información del Archivo
            $table->string('file_path');
            $table->string('file_type')->default('file'); // pdf, video, image, zip
            $table->string('file_size')->nullable(); 
            
            // Organización
            $table->string('category'); 
            
           
            $table->integer('downloads')->default(0);
            
            $table->timestamps();
        });

        
        Schema::create('resource_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rating'); 
            $table->timestamps();
            
            
            $table->unique(['resource_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('resource_ratings');
        Schema::dropIfExists('resources');
    }
};