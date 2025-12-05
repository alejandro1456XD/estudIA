<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        //  CONVERSACIONES 
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); 
            $table->boolean('is_group')->default(false);
            $table->string('icon')->nullable(); 
            $table->foreignId('admin_id')->nullable()->constrained('users'); 
            $table->timestamps();
        });

        //  PARTICIPANTES 
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('last_read_at')->nullable(); 
            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);
        });

        // MENSAJES
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); 
            $table->text('body')->nullable(); 
            $table->string('attachment')->nullable(); 
            $table->string('type')->default('text'); 
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