<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contas_receber', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('market_place_id')->constrained('market_places');
            // Null quando o lançamento é manual (não veio de uma venda)
            $table->foreignId('venda_id')->nullable()->constrained('vendas');
            $table->string('descricao');
            $table->decimal('valor_bruto', 10, 2);
            $table->decimal('taxa_marketplace', 10, 2)->default(0);
            $table->decimal('valor_frete', 10, 2)->default(0);
            $table->decimal('valor_liquido', 10, 2);
            $table->date('previsao_recebimento');
            $table->date('data_recebimento_real')->nullable();
            $table->enum('status', ['pendente', 'recebido', 'atrasado'])->default('pendente');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['status', 'previsao_recebimento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contas_receber');
    }
};
