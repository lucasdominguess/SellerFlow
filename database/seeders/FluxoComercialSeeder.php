<?php

namespace Database\Seeders;

use App\Contracts\Services\Finance\AccountPayableServiceInterface;
use App\Contracts\Services\Finance\AccountReceivableServiceInterface;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\Enums\TransactionStatus;
use App\Models\Accout\Store;
use App\Models\Accout\User;
use App\Models\Business\Product;
use App\Models\Business\Supplier;
use App\Models\Finance\AccountPayable;
use App\Models\Finance\AccountReceivable;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use App\Models\Purchases\Purchase;
use App\Models\Purchases\PurchaseItem;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

// Seeder único e sincronizado: gera compras -> estoque -> vendas -> contas,
// reaproveitando os mesmos services de produção (estoque e financeiro) para que
// os dados fake se comportem exatamente como o app real. As vendas só vendem o
// que foi comprado (qty <= saldo), deixando estoque positivo para alimentar a
// análise de "dinheiro parado" (stock_investment_view).
class FluxoComercialSeeder extends Seeder
{
    private StockServiceInterface $stockService;
    private AccountPayableServiceInterface $payableService;
    private AccountReceivableServiceInterface $receivableService;

    // Saldo de estoque em memória por produto: alimentado pelas compras e
    // consumido pelas vendas (espelha stock_balances, evita vender sem saldo).
    private array $stockMap = [];
    // Último custo de compra por produto, usado para precificar a venda com markup.
    private array $costMap = [];

    public function run(): void
    {
        $this->stockService      = app(StockServiceInterface::class);
        $this->payableService    = app(AccountPayableServiceInterface::class);
        $this->receivableService = app(AccountReceivableServiceInterface::class);

        $company = Company::query()->first();
        $store   = Store::query()->first();
        $user    = User::query()->first();

        $suppliers    = Supplier::query()->pluck('id')->all();
        $marketplaces = MarketPlace::query()->pluck('id')->all();
        $products     = Product::query()->inRandomOrder()->limit(28)->get();

        if (! $company || ! $store || ! $user || empty($suppliers) || empty($marketplaces) || $products->isEmpty()) {
            $this->command?->warn('FluxoComercialSeeder: rode os seeders base antes (Company/Store/User/Supplier/MarketPlace/Product). Abortando.');
            return;
        }

        $this->seedCompras($company, $store, $user, $suppliers, $products, 20);
        $this->seedVendas($company, $store, $user, $marketplaces, 30);
        $this->seedContasAvulsas($company, $store, 8, 5);

        $this->command?->info('FluxoComercial: compras, vendas, estoque e contas gerados de forma sincronizada.');
    }

    private function seedCompras(Company $company, Store $store, User $user, array $suppliers, $products, int $qtd): void
    {
        for ($i = 0; $i < $qtd; $i++) {
            // Datas no passado para ficarem antes das vendas (FIFO coerente).
            $dataCompra = Carbon::now()->subDays(rand(60, 240));
            $concluida  = rand(1, 100) <= 70; // 70% concluída/paga, 30% pendente

            $purchase = Purchase::factory()->create([
                'company_id'    => $company->id,
                'store_id'      => $store->id,
                'fornecedor_id' => $suppliers[array_rand($suppliers)],
                'user_id'       => $user->id,
                'status'        => ($concluida ? TransactionStatus::COMPLETED : TransactionStatus::PENDING)->value,
                'data_compra'   => $dataCompra->toDateString(),
            ]);

            $escolhidos = $products->random(rand(1, min(5, $products->count())));
            $itens      = [];

            foreach ($escolhidos as $product) {
                $quantidade    = rand(20, 120);
                $valorUnitario = round(rand(500, 30000) / 100, 2); // R$ 5,00 a R$ 300,00

                $itens[] = PurchaseItem::factory()->create([
                    'compra_id'      => $purchase->id,
                    'product_id'     => $product->id,
                    'quantidade'     => $quantidade,
                    'valor_unitario' => $valorUnitario,
                    'valor_total'    => round($quantidade * $valorUnitario, 2),
                ]);

                $this->stockMap[$product->id] = ($this->stockMap[$product->id] ?? 0) + $quantidade;
                $this->costMap[$product->id]  = $valorUnitario;
            }

            // valor_total da compra = soma dos itens (igual ao service real).
            $purchase->update(['valor_total' => collect($itens)->sum('valor_total')]);

            // Efeitos colaterais reais: entradas de estoque + conta a pagar.
            $this->stockService->proccessItensPurchase($purchase, $itens);
            $conta = $this->payableService->proccessPurchase($purchase);

            $this->ajustarContaPagar($conta, $dataCompra, $concluida);
        }
    }

    private function seedVendas(Company $company, Store $store, User $user, array $marketplaces, int $qtd): void
    {
        for ($i = 0; $i < $qtd; $i++) {
            $disponiveis = array_keys(array_filter($this->stockMap, fn ($saldo) => $saldo > 0));
            if (empty($disponiveis)) {
                break;
            }

            $dataVenda = Carbon::now()->subDays(rand(1, 55));
            $recebida  = rand(1, 100) <= 75; // 75% concluída/recebida

            shuffle($disponiveis);
            $selecionados = array_slice($disponiveis, 0, rand(1, min(4, count($disponiveis))));

            $itensData = [];
            foreach ($selecionados as $productId) {
                $saldo = $this->stockMap[$productId];
                // Vende só parte do saldo: deixa estoque parado para a análise.
                $vender = max(1, (int) floor($saldo * (rand(20, 60) / 100)));
                $vender = min($vender, $saldo);

                $custo      = $this->costMap[$productId] ?? 10.0;
                $precoVenda = round($custo * (rand(130, 200) / 100), 2); // markup 30%-100%

                $itensData[] = [
                    'product_id'     => $productId,
                    'quantidade'     => $vender,
                    'valor_unitario' => $precoVenda,
                ];
            }

            $valorBruto = round(collect($itensData)->sum(fn ($it) => $it['quantidade'] * $it['valor_unitario']), 2);
            $taxa       = round($valorBruto * (rand(10, 20) / 100), 2);
            $frete      = round(rand(0, 3000) / 100, 2);
            $previsao   = (clone $dataVenda)->addDays(rand(5, 30));

            $sale = Sale::factory()->create([
                'company_id'            => $company->id,
                'store_id'              => $store->id,
                'market_place_id'       => $marketplaces[array_rand($marketplaces)],
                'user_id'               => $user->id,
                'data_venda'            => $dataVenda->toDateString(),
                'valor_bruto'           => $valorBruto,
                'taxa_marketplace'      => $taxa,
                'valor_frete'           => $frete,
                'valor_liquido'         => round($valorBruto - $taxa - $frete, 2),
                'data_previsao_repasse' => $previsao->toDateString(),
                'status'                => ($recebida ? TransactionStatus::COMPLETED : TransactionStatus::PENDING)->value,
            ]);

            $itens = [];
            foreach ($itensData as $it) {
                $itens[] = SaleItem::factory()->create([
                    'venda_id'       => $sale->id,
                    'product_id'     => $it['product_id'],
                    'quantidade'     => $it['quantidade'],
                    'valor_unitario' => $it['valor_unitario'],
                    'valor_total'    => round($it['quantidade'] * $it['valor_unitario'], 2),
                ]);

                $this->stockMap[$it['product_id']] -= $it['quantidade'];
            }

            // Efeitos colaterais reais: saídas de estoque + conta a receber.
            $this->stockService->proccessItensSale($sale, $itens);
            $conta = $this->receivableService->proccessSale($sale);

            $this->ajustarContaReceber($conta, $previsao, $recebida);
        }
    }

    // Despesas e receitas avulsas (sem origem em compra/venda), para enriquecer o financeiro.
    private function seedContasAvulsas(Company $company, Store $store, int $qtdPagar, int $qtdReceber): void
    {
        $despesas = ['Aluguel do galpão', 'Energia elétrica', 'Internet', 'Embalagens', 'Sistema de gestão', 'Contador', 'Material de escritório'];
        $receitas = ['Reembolso marketplace', 'Venda de sucata', 'Acerto de fornecedor', 'Outras receitas'];

        for ($i = 0; $i < $qtdPagar; $i++) {
            $venc = Carbon::now()->addDays(rand(-30, 40));
            [$status, $pagoEm] = $this->statusPorData($venc);

            AccountPayable::create([
                'valor'                   => round(rand(8000, 250000) / 100, 2),
                'vencimento'              => $venc->toDateString(),
                'pago_em'                 => $pagoEm,
                'status'                  => $status,
                'categoria_financeira_id' => 2, // SAIDA
                'forma_pagamento_id'      => rand(1, 5),
                'origem_tipo'             => 'ajuste_manual',
                'origem_id'               => null,
                'company_id'              => $company->id,
                'observacao'              => $despesas[array_rand($despesas)],
            ]);
        }

        for ($i = 0; $i < $qtdReceber; $i++) {
            $prev = Carbon::now()->addDays(rand(-25, 35));
            [$status, $recebidoEm] = $this->statusPorData($prev);

            AccountReceivable::create([
                'valor'                => round(rand(5000, 150000) / 100, 2),
                'previsao_recebimento' => $prev->toDateString(),
                'recebido_em'          => $recebidoEm,
                'status'               => $status,
                'origem_tipo'          => 'ajuste_manual',
                'origem_id'            => null,
                'company_id'           => $company->id,
                'store_id'             => $store->id,
                'observacao'           => $receitas[array_rand($receitas)],
            ]);
        }
    }

    private function ajustarContaPagar(AccountPayable $conta, Carbon $dataCompra, bool $paga): void
    {
        $vencimento = (clone $dataCompra)->addDays(30);

        if ($paga) {
            $conta->update([
                'status'     => TransactionStatus::COMPLETED->value,
                'vencimento' => $vencimento->toDateString(),
                'pago_em'    => (clone $dataCompra)->addDays(rand(1, 25))->toDateString(),
            ]);

            return;
        }

        // Pendente: metade vira atrasada (vencimento no passado), metade futura.
        $atrasada = (bool) rand(0, 1);
        $venc     = $atrasada ? Carbon::now()->subDays(rand(1, 30)) : Carbon::now()->addDays(rand(5, 40));

        $conta->update([
            'status'     => ($atrasada ? TransactionStatus::OVERDUE : TransactionStatus::PENDING)->value,
            'vencimento' => $venc->toDateString(),
            'pago_em'    => null,
        ]);
    }

    private function ajustarContaReceber(AccountReceivable $conta, Carbon $previsao, bool $recebida): void
    {
        if ($recebida) {
            $conta->update([
                'status'      => TransactionStatus::COMPLETED->value,
                'recebido_em' => (clone $previsao)->addDays(rand(0, 5))->toDateString(),
            ]);

            return;
        }

        // Pendente: se a previsão já passou, conta como atrasada.
        $conta->update([
            'status'      => ($previsao->isPast() ? TransactionStatus::OVERDUE : TransactionStatus::PENDING)->value,
            'recebido_em' => null,
        ]);
    }

    // 60% quitada (com data próxima do vencimento); senão atrasada se vencida, ou pendente.
    private function statusPorData(Carbon $data): array
    {
        if (rand(1, 100) <= 60) {
            return [TransactionStatus::COMPLETED->value, (clone $data)->addDays(rand(0, 3))->toDateString()];
        }

        return [($data->isPast() ? TransactionStatus::OVERDUE : TransactionStatus::PENDING)->value, null];
    }
}
