<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movimentacoes_estoque', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('tipo', ['entrada', 'saida', 'ajuste']);
            // Sempre positivo; o tipo define se soma ou subtrai
            $table->integer('quantidade');
            // Sem FK real: origem_id aponta para compras, vendas ou ajustes_estoque conforme origem_tipo
            $table->enum('origem_tipo', ['compra', 'venda', 'ajuste_manual']);
            $table->unsignedBigInteger('origem_id');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index(['origem_tipo', 'origem_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_estoque');
    }
};
