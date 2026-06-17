<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Adiciona company_id em todas as tabelas de transação para suportar multi-tenancy futuro.
// vendas já tem store_id → company derivável, mas FK direta melhora consultas e isolamento.
return new class extends Migration {
    public function up(): void
    {
        // default(1) permite rodar em tabelas que já têm dados (MVP tem uma única empresa, id=1)
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('company_id')->default(1)->after('id')->constrained('companies');
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->foreignId('company_id')->default(1)->after('id')->constrained('companies');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('company_id')->default(1)->after('id')->constrained('companies');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
