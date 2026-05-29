<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Substitui origem_tipo + origem_id pelo FK direto compra_id,
// espelhando o padrão de contas_receber que tem venda_id nullable.
return new class extends Migration {
    public function up(): void
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropColumn(['origem_tipo', 'origem_id']);
            $table->foreignId('compra_id')->nullable()->after('fornecedor_id')->constrained('compras');
        });
    }

    public function down(): void
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropForeign(['compra_id']);
            $table->dropColumn('compra_id');
            $table->enum('origem_tipo', ['compra', 'manual'])->default('manual')->after('data_pagamento');
            $table->unsignedBigInteger('origem_id')->nullable()->after('origem_tipo');
        });
    }
};
