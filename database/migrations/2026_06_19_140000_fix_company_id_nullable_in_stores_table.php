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
        // A migration original encadeava ->constrained('companies')->nullable(): constrained()
        // retorna a definição da foreign key, não a coluna, então o nullable() foi ignorado e a
        // coluna ficou NOT NULL — embora StoreCreateRequest trate company_id como opcional.
        Schema::table('stores', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable(false)->change();
        });
    }
};
