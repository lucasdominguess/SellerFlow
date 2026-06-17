<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_payables', function (Blueprint $table) {
            $table->id();
            $table->decimal('valor', 10, 2);
            $table->date('vencimento')->nullable();
            $table->date('pago_em')->nullable();
            $table->enum('status', ['pendente', 'concluido', 'atrasado', 'cancelado'])->default('pendente');
            $table->foreignId('categoria_financeira_id')->constrained('financial_categories')->nullable();
            $table->foreignId('forma_pagamento_id')->constrained('payment_methods')->nullable();
            $table->enum('origem_tipo', ['compra', 'venda', 'ajuste_manual'])->default('ajuste_manual');
            $table->unsignedBigInteger('origem_id')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['status', 'vencimento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_payables');
    }
};
