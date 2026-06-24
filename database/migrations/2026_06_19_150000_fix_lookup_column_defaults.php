<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Restaura os defaults perdidos em colunas FK de lookup. Nas migrations originais o
// ->default(N) vinha DEPOIS do ->constrained(): constrained() devolve a definição da FK,
// não da coluna, então o default era descartado e a coluna ficava NOT NULL sem default.
// users.status_id e companies.status_id já estão cobertos por $attributes nos models e
// ficam de fora aqui. As colunas seguem NOT NULL — apenas ganham o default pretendido.
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreignId('status_id')->default(1)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('status_id')->default(1)->change();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->foreignId('status_id')->default(1)->change();
        });

        Schema::table('market_places', function (Blueprint $table) {
            $table->foreignId('status_id')->default(1)->change();
        });

        Schema::table('company_users', function (Blueprint $table) {
            $table->foreignId('role_id')->default(1)->change();
            $table->foreignId('status_id')->default(1)->change();
        });

        Schema::table('user_stores', function (Blueprint $table) {
            $table->foreignId('role_id')->default(1)->change();
            $table->foreignId('status_id')->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Volta ao estado original: NOT NULL sem default.
        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreignId('status_id')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('status_id')->change();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->foreignId('status_id')->change();
        });

        Schema::table('market_places', function (Blueprint $table) {
            $table->foreignId('status_id')->change();
        });

        Schema::table('company_users', function (Blueprint $table) {
            $table->foreignId('role_id')->change();
            $table->foreignId('status_id')->change();
        });

        Schema::table('user_stores', function (Blueprint $table) {
            $table->foreignId('role_id')->change();
            $table->foreignId('status_id')->change();
        });
    }
};
