<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // A view usa SQL exclusivo do Postgres (jsonb_agg, FILTER, window functions).
        // Em outros drivers (ex.: SQLite dos testes) é ignorada — o cálculo de
        // investimento só é suportado em Postgres, ambiente real da aplicação.
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // Valor "parado" no estoque por produto/empresa, calculado via FIFO.
        // Premissa FIFO: o que sai primeiro é o mais antigo; logo o que SOBRA é o mais recente.
        // Para valorizar o saldo_atual, percorremos as camadas de compra da mais nova para a
        // mais antiga, preenchendo a quantidade que ainda existe em estoque e somando cada
        // unidade pelo preço real da sua camada (sem custo médio, sem "último preço").
        DB::statement(<<<'SQL'
            CREATE VIEW stock_investment_view AS
            WITH balances AS (
                SELECT company_id, product_id, saldo_atual
                FROM stock_balances
                WHERE saldo_atual > 0
            ),
            -- Camadas de compra (uma por item de compra), com a quantidade acumulada
            -- da mais nova para a mais antiga. Compras canceladas são ignoradas.
            layers AS (
                SELECT
                    pu.company_id,
                    pi.product_id,
                    pi.id,
                    pi.valor_unitario,
                    pi.quantidade,
                    pu.data_compra,
                    SUM(pi.quantidade) OVER (
                        PARTITION BY pu.company_id, pi.product_id
                        ORDER BY pu.data_compra DESC, pi.id DESC
                        ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
                    ) AS cum_qty
                FROM purchase_items pi
                JOIN purchases pu ON pu.id = pi.compra_id
                WHERE pu.status <> 'cancelado'
            ),
            -- Quantas unidades de cada camada cabem dentro do saldo_atual (janela FIFO).
            remaining AS (
                SELECT
                    l.company_id,
                    l.product_id,
                    l.valor_unitario,
                    l.data_compra,
                    l.id,
                    GREATEST(0, LEAST(l.quantidade, b.saldo_atual - (l.cum_qty - l.quantidade))) AS qty_remaining
                FROM layers l
                JOIN balances b
                    ON b.company_id = l.company_id
                   AND b.product_id = l.product_id
            ),
            aggregated AS (
                SELECT
                    company_id,
                    product_id,
                    SUM(qty_remaining * valor_unitario) AS valor_investido,
                    SUM(qty_remaining) AS qty_coberta,
                    jsonb_agg(
                        jsonb_build_object('qty', qty_remaining, 'preco', valor_unitario)
                        ORDER BY data_compra DESC, id DESC
                    ) FILTER (WHERE qty_remaining > 0) AS composicao
                FROM remaining
                GROUP BY company_id, product_id
            )
            SELECT
                b.company_id,
                b.product_id,
                b.saldo_atual,
                COALESCE(a.valor_investido, 0)::numeric(14,2) AS valor_investido,
                COALESCE(a.composicao, '[]'::jsonb) AS composicao,
                -- Unidades sem camada de compra (ex.: entraram por ajuste positivo) ficam sem custo real.
                (b.saldo_atual > COALESCE(a.qty_coberta, 0))::int AS tem_unidade_sem_custo
            FROM balances b
            LEFT JOIN aggregated a
                ON a.company_id = b.company_id
               AND a.product_id = b.product_id;
        SQL);
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS stock_investment_view');
    }
};
