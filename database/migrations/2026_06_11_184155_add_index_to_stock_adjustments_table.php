<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            // Serve a subquery "last_adjustment_user" de checkQuantityProductsInStock:
            // filtra company_id + product_id por igualdade e ordena por created_at desc (LIMIT 1).
            // Sem este índice, cada produto do resultado dispara um seq scan + sort da tabela.
            $table->index(['company_id', 'product_id', 'created_at'], 'stock_adjustments_company_product_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropIndex('stock_adjustments_company_product_created_idx');
        });
    }
};
