<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pet_level')) {
                $table->integer('pet_level')->default(1);
            }
            if (!Schema::hasColumn('users', 'pet_xp')) {
                $table->integer('pet_xp')->default(0);
            }
            if (!Schema::hasColumn('users', 'pet_xp_next_level')) {
                $table->integer('pet_xp_next_level')->default(100);
            }
        });

        
        if (!Schema::hasTable('shop_items')) {
            Schema::create('shop_items', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('category'); 
                $table->string('pet_type_restriction')->nullable();
                $table->integer('price');
                $table->integer('xp_reward')->default(0);
                $table->string('image_path');
                $table->timestamps();
            });
        }

        
        if (!Schema::hasTable('user_items')) {
            Schema::create('user_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
               
                $table->foreignId('shop_item_id')->constrained('shop_items')->onDelete('cascade');
                $table->boolean('is_equipped')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        
        Schema::dropIfExists('user_items');
        Schema::dropIfExists('shop_items');
        
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'pet_level')) $table->dropColumn('pet_level');
            if (Schema::hasColumn('users', 'pet_xp')) $table->dropColumn('pet_xp');
            if (Schema::hasColumn('users', 'pet_xp_next_level')) $table->dropColumn('pet_xp_next_level');
        });
    }
};