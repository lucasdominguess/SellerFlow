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
        // definição da foreign key, não a coluna, então o nullable() era ignorado e a coluna
        // ficou NOT NULL de fato — embora ProductCreateRequest trate fornecedor_id como opcional.
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('fornecedor_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('fornecedor_id')->nullable(false)->change();
        });
    }
};
