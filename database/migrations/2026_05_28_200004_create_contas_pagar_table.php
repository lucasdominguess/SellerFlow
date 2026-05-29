<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contas_pagar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('categoria_financeira_id')->constrained('categoria_financeiras');
            $table->foreignId('forma_pagamento_id')->constrained('forma_pagamentos');
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores');
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->date('vencimento');
            $table->enum('status', ['pendente', 'pago', 'atrasado'])->default('pendente');
            $table->date('data_pagamento')->nullable();
            // Referência lógica: 'compra' = gerada automaticamente, 'manual' = lançamento direto
            $table->enum('origem_tipo', ['compra', 'manual'])->default('manual');
            $table->unsignedBigInteger('origem_id')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['status', 'vencimento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contas_pagar');
    }
};
