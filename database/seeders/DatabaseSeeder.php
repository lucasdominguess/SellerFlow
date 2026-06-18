<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StatusSeeder::class,              // status (sem FK)
            RoleSeeder::class,                // roles (sem FK)
            MarketPlaceSeeder::class,         // market_places → status
            CompanySeeder::class,             // companies → status
            StoreSeeder::class,               // stores → status, marketplace, company
            UserSeeder::class,                // users → status
            UserStoreSeeder::class,           // user_stores → users, stores, roles, status
            CompanyUserSeeder::class,         // company_users → company, user, role, status
            CategoriaFinanceiraSeeder::class, // financial_categories (sem FK)
            FormaPagamentoSeeder::class,      // payment_methods (sem FK)
            FornecedorSeeder::class,          // suppliers → status
            ProductSeeder::class,             // products → status, suppliers

            // Fluxo comercial sincronizado: compras → estoque → vendas → contas (a pagar/receber).
            // Reaproveita os services reais, então alimenta stock_balances e a stock_investment_view.
            FluxoComercialSeeder::class,
            // AjusteEstoqueSeeder::class,       // stock_adjustments + stock_movements → company, product, user

        ]);
    }
}
