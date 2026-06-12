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
        Schema::create('account_receivables', function (Blueprint $table) {
            $table->id();
            $table->decimal('valor', 10, 2);
            $table->date('previsao_recebimento')->nullable();
            $table->date('recebido_em')->nullable();
            $table->enum('status', ['pendente', 'concluido', 'atrasado', 'cancelado'])->default('pendente');
            $table->enum('origem_tipo', ['compra', 'venda', 'ajuste_manual'])->default('ajuste_manual');
            $table->unsignedBigInteger('origem_id')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['origem_tipo', 'origem_id']);
            $table->index(['status', 'previsao_recebimento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_receivables');
    }
};
