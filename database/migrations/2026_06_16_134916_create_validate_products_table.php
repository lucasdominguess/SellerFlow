<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
Schema::create('validate_products', function (Blueprint $table) {
    $table->id();

    // ownership — alinhe com o multi-tenant do teu sgopm-api
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    // identificação
    $table->string('name');
    $table->string('brand')->nullable();
    $table->text('description')->nullable();
    $table->string('catalog_link')->nullable();


    $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->nullOnDelete();

 
    $table->decimal('price_sale', 10, 2);
    $table->decimal('price_buy', 10, 2);
    $table->decimal('cust_additional', 10, 2)->default(0);

    // snapshot da taxa Shopee usada no cálculo (torna o registro reproduzível)
    $table->decimal('fee_percent', 5, 2)->default(20.00);
    $table->decimal('fee_fixed', 10, 2)->default(4.00);

    // resultados (snapshot — calculados por Service no save, não pelo cliente)
    $table->decimal('profit_amount', 10, 2);   // lucro_reais
    $table->decimal('profit_margin', 5, 2);    // lucro_percent
    $table->decimal('breakeven_roas', 6, 2);   // roas_empate

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validate_products');
    }
};
