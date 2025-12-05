<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_items', function (Blueprint $table) {
            
            if (Schema::hasColumn('shop_items', 'type')) {
                $table->renameColumn('type', 'category');
            } elseif (!Schema::hasColumn('shop_items', 'category')) {
                
                $table->string('category')->after('name');
            }

            
            if (!Schema::hasColumn('shop_items', 'pet_type_restriction')) {
                $table->string('pet_type_restriction')->nullable()->after('category');
            }
            
            if (!Schema::hasColumn('shop_items', 'xp_reward')) {
                $table->integer('xp_reward')->default(0)->after('price');
            }
        });
    }

    public function down(): void
    {
        
        Schema::table('shop_items', function (Blueprint $table) {
            if (Schema::hasColumn('shop_items', 'category')) {
                $table->renameColumn('category', 'type');
            }
            $table->dropColumn(['pet_type_restriction', 'xp_reward']);
        });
    }
};