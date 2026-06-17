<?php

namespace Tests\Feature\Stock;

use App\Models\Accout\User;
use App\Models\Business\Supplier;
use App\Models\Business\Product;
use App\Models\ListSuspended\Company;
use App\Models\Stock\Stock;
use App\Models\Stock\StockAdjustment;
use App\Repositories\Stock\StockBalanceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// Cria uma movimentação disparando o StockObserver (que recalcula o saldo materializado).
function makeMovement(int $companyId, int $productId, int $userId, string $tipo, int $qtd, string $origemTipo, int $origemId): Stock
{
    return Stock::create([
        'company_id'  => $companyId,
        'product_id'  => $productId,
        'user_id'     => $userId,
        'tipo'        => $tipo,
        'quantidade'  => $qtd,
        'origem_tipo' => $origemTipo,
        'origem_id'   => $origemId,
        'observacao'  => null,
    ]);
}

describe('StockBalance (saldo materializado)', function () {

    beforeEach(function () {
        // FKs para a tabela status: companies.status_id=1, users.status_id=2, products.status_id 1-3
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Empresa Ativa'],
            ['id' => 2, 'name' => 'Usuario Ativo'],
            ['id' => 3, 'name' => 'Produto Inativo'],
        ]);

        $this->company    = Company::factory()->create();
        $this->user       = User::factory()->create();
        $fornecedor       = Supplier::factory()->create();
        $this->product    = Product::factory()->create(['status_id' => 1, 'fornecedor_id' => $fornecedor->id]);
    });

    // O observer deve manter os 4 totais e o saldo coerentes com as movimentações criadas.
    it('mantém totais e saldo corretos ao criar movimentações', function () {
        $companyId = $this->company->id;
        $productId = $this->product->id;
        $userId    = $this->user->id;

        makeMovement($companyId, $productId, $userId, 'entrada', 10, 'compra', 1);
        makeMovement($companyId, $productId, $userId, 'saida', 3, 'venda', 1);

        $adjPos = StockAdjustment::create([
            'company_id' => $companyId, 'product_id' => $productId, 'user_id' => $userId,
            'quantidade' => 5, 'motivo' => 'devolucao', 'observacao' => null,
        ]);
        makeMovement($companyId, $productId, $userId, 'ajuste', 5, 'ajuste_manual', $adjPos->id);

        // Último ajuste feito por outro usuário — deve ser o que aparece em last_adjustment_user_id
        $user2  = User::factory()->create();
        $adjNeg = StockAdjustment::create([
            'company_id' => $companyId, 'product_id' => $productId, 'user_id' => $user2->id,
            'quantidade' => -2, 'motivo' => 'perda', 'observacao' => null,
        ]);
        makeMovement($companyId, $productId, $user2->id, 'ajuste', 2, 'ajuste_manual', $adjNeg->id);

        $balance = DB::table('stock_balances')
            ->where('company_id', $companyId)
            ->where('product_id', $productId)
            ->first();

        expect($balance)->not->toBeNull()
            ->and((int) $balance->total_entradas)->toBe(10)
            ->and((int) $balance->total_saidas)->toBe(3)
            ->and((int) $balance->total_ajustes_positivos)->toBe(5)
            ->and((int) $balance->total_ajustes_negativos)->toBe(2)
            ->and((int) $balance->saldo_atual)->toBe(10) // 10 - 3 + 5 - 2
            ->and((int) $balance->last_adjustment_user_id)->toBe($user2->id);
    });

    // Excluir uma movimentação deve recalcular o saldo (observer deleted).
    it('recalcula o saldo ao deletar uma movimentação', function () {
        $companyId = $this->company->id;
        $productId = $this->product->id;
        $userId    = $this->user->id;

        makeMovement($companyId, $productId, $userId, 'entrada', 10, 'compra', 1);
        $saida = makeMovement($companyId, $productId, $userId, 'saida', 4, 'venda', 1);

        $saida->delete();

        $balance = DB::table('stock_balances')
            ->where('company_id', $companyId)->where('product_id', $productId)->first();

        expect((int) $balance->saldo_atual)->toBe(10)
            ->and((int) $balance->total_saidas)->toBe(0);
    });

    // Sem movimentações restantes, a linha de saldo deve ser removida (paridade com a listagem antiga).
    it('remove a linha de saldo quando o produto fica sem movimentações', function () {
        $companyId = $this->company->id;
        $productId = $this->product->id;

        $mov = makeMovement($companyId, $productId, $this->user->id, 'entrada', 5, 'compra', 1);
        expect(DB::table('stock_balances')->where('product_id', $productId)->exists())->toBeTrue();

        $mov->delete();
        expect(DB::table('stock_balances')->where('product_id', $productId)->exists())->toBeFalse();
    });

    // O comando de rebuild deve reconstruir o saldo a partir do zero.
    it('reconstrói o saldo via stock:rebuild-balances', function () {
        $companyId = $this->company->id;
        $productId = $this->product->id;
        $userId    = $this->user->id;

        makeMovement($companyId, $productId, $userId, 'entrada', 8, 'compra', 1);
        makeMovement($companyId, $productId, $userId, 'saida', 3, 'venda', 1);

        // Zera a tabela e reconstrói a partir das movimentações
        DB::table('stock_balances')->delete();
        Artisan::call('stock:rebuild-balances');

        $balance = DB::table('stock_balances')
            ->where('company_id', $companyId)->where('product_id', $productId)->first();

        expect((int) $balance->saldo_atual)->toBe(5);
    });

    // A leitura paginada deve devolver o contrato esperado, incluindo o nome do último ajustante.
    it('paginate devolve o contrato esperado com o nome do último ajustante', function () {
        $companyId = $this->company->id;
        $productId = $this->product->id;
        $userId    = $this->user->id;

        $adj = StockAdjustment::create([
            'company_id' => $companyId, 'product_id' => $productId, 'user_id' => $userId,
            'quantidade' => 7, 'motivo' => 'devolucao', 'observacao' => null,
        ]);
        makeMovement($companyId, $productId, $userId, 'ajuste', 7, 'ajuste_manual', $adj->id);

        $paginator = app(StockBalanceRepository::class)
            ->paginate($companyId, null, null, null, 15, 1);

        expect($paginator->total())->toBe(1);

        $row = $paginator->items()[0];
        expect($row->product_name)->toBe($this->product->name)
            ->and($row->company_name)->toBe($this->company->name)
            ->and($row->last_adjustment_user)->toBe($this->user->name)
            ->and((int) $row->saldo_atual)->toBe(7);
    });
});
