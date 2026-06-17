<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\Sales\VendasRepositoryInterface;
use App\Contracts\Services\Finance\AccountReceivableServiceInterface;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\DTOs\Sales\VendasDTO;
use App\DTOs\Sales\VendasResponseDTO;
use App\Models\Sales\Sale;
use App\Services\Sales\VendasService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

describe('VendasService', function () {

    beforeEach(function () {
        $this->repositoryMock               = $this->createMock(VendasRepositoryInterface::class);
        $this->stockServiceMock             = $this->createMock(StockServiceInterface::class);
        $this->accountReceivableServiceMock = $this->createMock(AccountReceivableServiceInterface::class);
        $this->service                      = new VendasService(
            $this->repositoryMock,
            $this->stockServiceMock,
            $this->accountReceivableServiceMock,
        );
    });

    // verifica que store calcula valor_liquido (bruto - taxa - frete), persiste a venda,
    // os itens e processa a saida do estoque
    it('stores venda with calculated valor_liquido, items and stock exit', function () {
        $dto = VendasDTO::fromCreateRequest([
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

        expect($result)->toBeInstanceOf(VendasResponseDTO::class)
            ->and($result->id)->toBe(1)
            ->and($result->valor_liquido)->toBe(160.00);
    });

    // verifica que show delega ao repository e retorna VendasResponseDTO
    it('returns VendasResponseDTO for existing venda on show', function () {
        $model = Sale::factory()->make(['id' => 5]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('show')
            ->with($model)
            ->willReturn($model);

        $result = $this->service->show($model);

        expect($result)->toBeInstanceOf(VendasResponseDTO::class)
            ->and($result->id)->toBe(5);
    });

    // verifica que update recalcula valor_liquido usando os valores atuais da venda
    // como fallback quando apenas valor_bruto e alterado
    it('recalculates valor_liquido using current venda values as fallback', function () {
        $dto      = VendasDTO::fromUpdateRequest(['valor_bruto' => 250.00]);
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

        expect($result)->toBeInstanceOf(VendasResponseDTO::class)
            ->and($result->valor_liquido)->toBe(225.00);
    });

    // verifica que update nao recalcula valor_liquido quando nenhum componente do valor muda
    it('does not recalculate valor_liquido when no value component changes', function () {
        $dto      = VendasDTO::fromUpdateRequest(['observacao' => 'Pedido com atraso']);
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

    // verifica que index transforma os itens do paginator em arrays de VendasResponseDTO
    it('transforms paginator items into VendasResponseDTO arrays on index', function () {
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
