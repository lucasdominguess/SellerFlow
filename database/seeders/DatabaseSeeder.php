<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // StatusSeeder::class,              // status (sem FK)
            // RoleSeeder::class,                // roles (sem FK)
            // MarketPlaceSeeder::class,         // market_places → status
            // CompanySeeder::class,             // companies → status
            // StoreSeeder::class,               // stores → status, marketplace, company
            // UserSeeder::class,                // users → status
            // UserStoreSeeder::class,           // user_stores → users, stores, roles, status
            // CompanyUserSeeder::class,         // company_users → company, user, role, status
            // CategoriaFinanceiraSeeder::class, // categoria_financeiras (sem FK)
            // FormaPagamentoSeeder::class,      // forma_pagamentos (sem FK)
            // FornecedorSeeder::class,          // fornecedores → status
            // ProductSeeder::class,             // products → status, fornecedores
            // CompraSeeder::class,              // compras + compra_itens → company, store, fornecedor, user, product
            VendaSeeder::class,               // vendas + venda_itens → company, store, market_place, user, product, status
            AjusteEstoqueSeeder::class,       // ajustes_estoque + movimentacoes_estoque → company, product, user
            ContaPagarSeeder::class,          // contas_pagar → company, user, categoria_financeira, forma_pagamento, fornecedor
            ContaReceberSeeder::class,        // contas_receber → company, user, market_place
        ]);
    }
}
