<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
        if (!Schema::hasTable('groups')) {
            Schema::create('groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('category')->nullable();
                $table->boolean('is_private')->default(false);
                
               
                $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
                
                $table->timestamps();
            });
        }

        
        if (!Schema::hasTable('group_user')) {
            Schema::create('group_user', function (Blueprint $table) {
                $table->id();
                
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('group_id')->constrained('groups')->onDelete('cascade'); 
                
                $table->string('role')->default('member');
                
                $table->timestamps();

                $table->unique(['user_id', 'group_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('group_user');
        Schema::dropIfExists('groups');
    }
};