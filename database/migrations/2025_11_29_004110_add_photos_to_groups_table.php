<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('group_photo_path')->nullable()->after('admin_id');
            $table->string('cover_photo_path')->nullable()->after('group_photo_path');
        });
    }

    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['group_photo_path', 'cover_photo_path']);
        });
    }
};