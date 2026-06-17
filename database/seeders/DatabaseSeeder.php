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
            // CompraSeeder::class,              // purchases + purchase_items → company, store, supplier, user, product
            // VendaSeeder::class,               // sales + sale_items → company, store, market_place, user, product, status
            // AjusteEstoqueSeeder::class,       // stock_adjustments + stock_movements → company, product, user

        ]);
    }
}
