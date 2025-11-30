<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. CONVERSACIONES (Puede ser privada o grupal)
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Solo para grupos
            $table->boolean('is_group')->default(false);
            $table->string('icon')->nullable(); // Foto del grupo
            $table->foreignId('admin_id')->nullable()->constrained('users'); // Creador del grupo
            $table->timestamps();
        });

        // 2. PARTICIPANTES (Quién está en qué conversación)
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('last_read_at')->nullable(); // Para saber si leyó los mensajes
            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);
        });

        // 3. MENSAJES
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // Quién lo envió
            $table->text('body')->nullable(); // Texto
            $table->string('attachment')->nullable(); // Foto/Archivo
            $table->string('type')->default('text'); // text, image, file
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversation_user');
        Schema::dropIfExists('conversations');
    }
};