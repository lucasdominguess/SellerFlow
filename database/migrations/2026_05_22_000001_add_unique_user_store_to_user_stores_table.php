<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_stores', function (Blueprint $table) {
            $table->unique(['user_id', 'store_id'], 'user_stores_user_id_store_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('user_stores', function (Blueprint $table) {
            $table->dropUnique('user_stores_user_id_store_id_unique');
        });
    }
};
