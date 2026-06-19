<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\Sales\SaleRepositoryInterface;
use App\Contracts\Services\Finance\AccountReceivableServiceInterface;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\DTOs\Sales\SaleDTO;
use App\DTOs\Sales\SaleResponseDTO;
use App\Exceptions\Stock\InsufficientStockException;
use App\Models\Sales\Sale;
use App\Services\Sales\SaleService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

describe('SaleService', function () {

    beforeEach(function () {
        $this->repositoryMock               = $this->createMock(SaleRepositoryInterface::class);
        $this->stockServiceMock             = $this->createMock(StockServiceInterface::class);
        $this->accountReceivableServiceMock = $this->createMock(AccountReceivableServiceInterface::class);
        $this->service                      = new SaleService(
            $this->repositoryMock,
            $this->stockServiceMock,
            $this->accountReceivableServiceMock,
        );
    });

    // verifica que store calcula valor_liquido (bruto - taxa - frete), persiste a venda,
    // os itens e processa a saida do estoque
    it('stores venda with calculated valor_liquido, items and stock exit', function () {
        $dto = SaleDTO::fromCreateRequest([
            'company_id'       => 1,
            'store_id'         => 1,
            'user_id'          => 1,
            'market_place_id'  => 1,
            'numero_pedido'    => '1234567890',
            'data_venda'       => '2026-06-01',
            'valor_bruto'      => 200.00,
            'taxa_marketplace' => 30.00,
            'valor_frete'      => 10.00,
            'venda_itens'      => [
                ['product_id' => 10, 'quantidade' => 2, 'valor_unitario' => 100.00],
            ],
        ]);

        $expectedData                  = $dto->toArray();
        $expectedData['valor_liquido'] = 160.00; // 200 - 30 - 10

        $model = Sale::factory()->make([
            'id'               => 1,
            'company_id'       => 1,
            'valor_bruto'      => 200.00,
            'taxa_marketplace' => 30.00,
            'valor_frete'      => 10.00,
            'valor_liquido'    => 160.00,
        ]);

        // saldo suficiente: produto 10 tem 10 em estoque, pedido de 2
        $this->stockServiceMock
            ->expects($this->once())
            ->method('lockAvailableQuantities')
            ->with(1, [10])
            ->willReturn([10 => 10]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('store')
            ->with($expectedData)
            ->willReturn($model);

        $this->repositoryMock
            ->expects($this->once())
            ->method('storeItens')
            ->with($model, $dto->venda_itens)
            ->willReturn($model);

        $this->stockServiceMock
            ->expects($this->once())
            ->method('proccessItensSale')
            ->with($model, $dto->venda_itens);

        $result = $this->service->store($dto);

        expect($result)->toBeInstanceOf(SaleResponseDTO::class)
            ->and($result->id)->toBe(1)
            ->and($result->valor_liquido)->toBe(160.00);
    });

    // bloqueia a venda quando a quantidade pedida excede o saldo disponível
    it('blocks sale when stock is insufficient', function () {
        $dto = SaleDTO::fromCreateRequest([
            'company_id'      => 1,
            'store_id'        => 1,
            'user_id'         => 1,
            'market_place_id' => 1,
            'numero_pedido'   => '1234567890',
            'data_venda'      => '2026-06-01',
            'valor_bruto'     => 200.00,
            'venda_itens'     => [
                ['product_id' => 10, 'quantidade' => 5, 'valor_unitario' => 40.00],
            ],
        ]);

        // saldo 3 < 5 pedido → deve bloquear
        $this->stockServiceMock
            ->expects($this->once())
            ->method('lockAvailableQuantities')
            ->with(1, [10])
            ->willReturn([10 => 3]);

        // venda nem chega a ser persistida
        $this->repositoryMock->expects($this->never())->method('store');

        expect(fn () => $this->service->store($dto))
            ->toThrow(InsufficientStockException::class);
    });

    // soma a quantidade do mesmo produto repetido em itens diferentes antes de checar o saldo
    it('sums quantities of the same product across items before checking stock', function () {
        $dto = SaleDTO::fromCreateRequest([
            'company_id'      => 1,
            'store_id'        => 1,
            'user_id'         => 1,
            'market_place_id' => 1,
            'numero_pedido'   => '1234567890',
            'data_venda'      => '2026-06-01',
            'valor_bruto'     => 200.00,
            'venda_itens'     => [
                ['product_id' => 10, 'quantidade' => 4, 'valor_unitario' => 40.00],
                ['product_id' => 10, 'quantidade' => 3, 'valor_unitario' => 40.00],
            ],
        ]);

        // total pedido = 7; saldo 5 < 7 → bloqueia (mesmo cada item isolado cabendo)
        $this->stockServiceMock
            ->expects($this->once())
            ->method('lockAvailableQuantities')
            ->with(1, [10])
            ->willReturn([10 => 5]);

        $this->repositoryMock->expects($this->never())->method('store');

        expect(fn () => $this->service->store($dto))
            ->toThrow(InsufficientStockException::class);
    });

    // verifica que show delega ao repository e retorna SaleResponseDTO
    it('returns SaleResponseDTO for existing venda on show', function () {
        $model = Sale::factory()->make(['id' => 5]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('show')
            ->with($model)
            ->willReturn($model);

        $result = $this->service->show($model);

        expect($result)->toBeInstanceOf(SaleResponseDTO::class)
            ->and($result->id)->toBe(5);
    });

    // verifica que update recalcula valor_liquido usando os valores atuais da venda
    // como fallback quando apenas valor_bruto e alterado
    it('recalculates valor_liquido using current venda values as fallback', function () {
        $dto      = SaleDTO::fromUpdateRequest(['valor_bruto' => 250.00]);
        $original = Sale::factory()->make([
            'id'               => 3,
            'valor_bruto'      => 200.00,
            'taxa_marketplace' => 20.00,
            'valor_frete'      => 5.00,
            'valor_liquido'    => 175.00,
        ]);

        $expectedData                  = $dto->toArray();
        $expectedData['valor_liquido'] = 225.00; // 250 - 20 - 5 (taxa e frete vêm do model atual)

        $updated = Sale::factory()->make([
            'id'               => 3,
            'valor_bruto'      => 250.00,
            'taxa_marketplace' => 20.00,
            'valor_frete'      => 5.00,
            'valor_liquido'    => 225.00,
        ]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('update')
            ->with($original, $expectedData)
            ->willReturn($updated);

        $result = $this->service->update($original, $dto);

        expect($result)->toBeInstanceOf(SaleResponseDTO::class)
            ->and($result->valor_liquido)->toBe(225.00);
    });

    // verifica que update nao recalcula valor_liquido quando nenhum componente do valor muda
    it('does not recalculate valor_liquido when no value component changes', function () {
        $dto      = SaleDTO::fromUpdateRequest(['observacao' => 'Pedido com atraso']);
        $original = Sale::factory()->make(['id' => 4, 'observacao' => null]);

        $expectedData = $dto->toArray();
        expect($expectedData)->not->toHaveKey('valor_liquido');

        $updated = Sale::factory()->make(['id' => 4, 'observacao' => 'Pedido com atraso']);

        $this->repositoryMock
            ->expects($this->once())
            ->method('update')
            ->with($original, $expectedData)
            ->willReturn($updated);

        $result = $this->service->update($original, $dto);

        expect($result->observacao)->toBe('Pedido com atraso');
    });

    // verifica que delete delega a exclusao ao repository uma unica vez
    it('delegates deletion to repository', function () {
        $model = Sale::factory()->make(['id' => 7]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('delete')
            ->with($model);

        $this->service->delete($model);
    });

    // verifica que index transforma os itens do paginator em arrays de SaleResponseDTO
    it('transforms paginator items into SaleResponseDTO arrays on index', function () {
        $model      = Sale::factory()->make(['id' => 1, 'numero_pedido' => '999', 'valor_liquido' => 99.90]);
        $collection = new Collection([$model]);
        $paginator  = new LengthAwarePaginator($collection, 1, 15, 1);

        $this->repositoryMock
            ->expects($this->once())
            ->method('index')
            ->with(15, 1, [])
            ->willReturn($paginator);

        $result = $this->service->index();
        $items  = $result->items();

        expect($items)->toBeArray()
            ->and($items[0])->toBeArray()
            ->and($items[0]['numero_pedido'])->toBe('999')
            ->and($items[0]['valor_liquido'])->toBe(99.90);
    });
});
