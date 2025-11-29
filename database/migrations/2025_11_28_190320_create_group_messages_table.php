<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Texto del mensaje
            $table->text('content')->nullable();

            // Archivo subido (imagen o cualquier otro)
            $table->string('file_path')->nullable();

            // Tipo de mensaje: text, image, file
            $table->string('type')->default('text');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_messages');
    }
};
