<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('total_entradas')->default(0);
            $table->integer('total_saidas')->default(0);
            $table->integer('total_ajustes_positivos')->default(0);
            $table->integer('total_ajustes_negativos')->default(0);
            $table->integer('saldo_atual')->default(0);
            $table->foreignId('last_adjustment_user_id')->nullable()->constrained('users');
            $table->timestamps();

            // Uma linha de saldo por produto/empresa; serve também o filtro WHERE company_id da listagem.
            $table->unique(['company_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_balances');
    }
};
