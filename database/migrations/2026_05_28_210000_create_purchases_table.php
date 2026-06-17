<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('store_id')->constrained('stores');
            $table->foreignId('fornecedor_id')->constrained('suppliers');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('forma_pagamento_id')->constrained('payment_methods');
            $table->enum('status', ['pendente', 'concluido', 'atrasado', 'cancelado'])->default('pendente');
            $table->string('numero_nota')->nullable();
            $table->date('data_compra');
            $table->decimal('valor_total', 10, 2);
            $table->integer('numero_parcelas')->default(1);
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
