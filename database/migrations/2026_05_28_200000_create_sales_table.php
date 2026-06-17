<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores');
            $table->foreignId('market_place_id')->constrained('market_places');
            $table->foreignId('user_id')->constrained('users');
            $table->string('numero_pedido');
            $table->date('data_venda');
            $table->decimal('valor_bruto', 10, 2);
            $table->decimal('taxa_marketplace', 10, 2)->default(0);
            $table->decimal('valor_frete', 10, 2)->default(0);
            $table->decimal('valor_liquido', 10, 2);
            $table->date('data_previsao_repasse')->nullable();
            $table->enum('status', ['pendente', 'concluido', 'atrasado', 'cancelado'])->default('pendente');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['market_place_id', 'numero_pedido']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
