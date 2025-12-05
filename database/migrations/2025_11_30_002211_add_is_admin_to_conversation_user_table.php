<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('conversation_user', function (Blueprint $table) {
           
            $table->boolean('is_admin')->default(false)->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('conversation_user', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};