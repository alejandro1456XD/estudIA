<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        
        Schema::table('users', function (Blueprint $table) {
            $table->integer('coins')->default(0);
            $table->string('pet_status')->default('happy');
        });

        
        Schema::create('shop_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); 
            $table->integer('price');
            $table->string('image_path'); 
            $table->timestamps();
        });

      
        Schema::create('user_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shop_item_id')->constrained('shop_items')->onDelete('cascade');
            $table->boolean('is_equipped')->default(false); 
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        

        
        Schema::dropIfExists('user_items');

        
        Schema::dropIfExists('shop_items');

       
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['coins', 'pet_status']);
        });
    }
};