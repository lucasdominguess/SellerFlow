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
        // A migration original encadeava ->constrained()->nullable(): constrained() retorna a
        // definição da foreign key, não a coluna, então o nullable() foi ignorado e as colunas
        // ficaram NOT NULL — embora AccountPayableCreateRequest as trate como opcionais.
        Schema::table('account_payables', function (Blueprint $table) {
            $table->foreignId('categoria_financeira_id')->nullable()->change();
            $table->foreignId('forma_pagamento_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_payables', function (Blueprint $table) {
            $table->foreignId('categoria_financeira_id')->nullable(false)->change();
            $table->foreignId('forma_pagamento_id')->nullable(false)->change();
        });
    }
};
