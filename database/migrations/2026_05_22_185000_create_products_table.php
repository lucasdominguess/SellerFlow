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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('marca')->nullable();
            $table->string('description')->nullable();
            $table->decimal('price_unit', 10, 2);
            $table->decimal('price_box', 10, 2);
            $table->foreignId('status_id')->constrained('status')->default(1);
            $table->foreignId('fornecedor_id')->constrained('suppliers')->nullable();
            $table->string('path_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
