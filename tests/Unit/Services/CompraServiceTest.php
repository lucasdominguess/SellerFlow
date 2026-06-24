<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\Purchases\PurchaseRepositoryInterface;
use App\Contracts\Services\Finance\AccountPayableServiceInterface;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\DTOs\Purchases\PurchaseDTO;
use App\DTOs\Purchases\PurchaseResponseDTO;
use App\Models\Purchases\Purchase;
use App\Services\Purchases\PurchaseService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

describe('PurchaseService', function () {

    beforeEach(function () {
        $this->repositoryMock            = $this->createMock(PurchaseRepositoryInterface::class);
        $this->stockServiceMock          = $this->createMock(StockServiceInterface::class);
        $this->accountPayableServiceMock = $this->createMock(AccountPayableServiceInterface::class);
        $this->service                   = new PurchaseService(
            $this->repositoryMock,
            $this->stockServiceMock,
            $this->accountPayableServiceMock,
        );
    });

    // verifica que store calcula valor_total a partir dos itens, persiste a compra,
    // os itens e processa a entrada no estoque
    it('cria compra com valor_total calculado, itens e entrada de estoque', function () {
        $dto = PurchaseDTO::fromCreateRequest([
            'company_id'         => 1,
            'store_id'           => 1,
            'user_id'            => 1,
            'fornecedor_id'      => 2,
            'forma_pagamento_id' => 1,
            'status_id'          => 1,
            'numero_nota'        => 'NF-001',
            'data_compra'        => '2026-06-01',
            'itens'              => [
                ['product_id' => 10, 'quantidade' => 2, 'valor_unitario' => 50.00],
                ['product_id' => 11, 'quantidade' => 3, 'valor_unitario' => 20.00],
            ],
        ]);

        $expectedData                = $dto->toArray();
        $expectedData['valor_total'] = 160.00; // (2*50.00) + (3*20.00)

        $model = Purchase::factory()->make([
            'id'          => 1,
            'company_id'  => 1,
            'valor_total' => 160.00,
        ]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('store')
            ->with($expectedData)
            ->willReturn($model);

        $this->repositoryMock
            ->expects($this->once())
            ->method('storeItens')
            ->with($model, $dto->itens)
            ->willReturn($model);

        $this->stockServiceMock
            ->expects($this->once())
            ->method('proccessItensPurchase')
            ->with($model, $dto->itens);

        $result = $this->service->store($dto);

        expect($result)->toBeInstanceOf(PurchaseResponseDTO::class)
            ->and($result->id)->toBe(1)
            ->and($result->valor_total)->toBe(160.00);
    });

    // verifica que show delega ao repository e retorna PurchaseResponseDTO
    it('retorna PurchaseResponseDTO ao exibir uma compra existente', function () {
        $model = Purchase::factory()->make(['id' => 5, 'valor_total' => 100.00]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('show')
            ->with($model)
            ->willReturn($model);

        $result = $this->service->show($model);

        expect($result)->toBeInstanceOf(PurchaseResponseDTO::class)
            ->and($result->id)->toBe(5);
    });

    // verifica que update repassa os dados ao repository e retorna PurchaseResponseDTO atualizado
    it('atualiza a compra e retorna o PurchaseResponseDTO atualizado', function () {
        $dto      = PurchaseDTO::fromUpdateRequest(['observacao' => 'Nota fiscal corrigida']);
        $original = Purchase::factory()->make(['id' => 3, 'observacao' => null]);
        $updated  = Purchase::factory()->make(['id' => 3, 'observacao' => 'Nota fiscal corrigida']);

        $this->repositoryMock
            ->expects($this->once())
            ->method('update')
            ->with($original, $dto->toArray())
            ->willReturn($updated);

        $result = $this->service->update($original, $dto);

        expect($result)->toBeInstanceOf(PurchaseResponseDTO::class)
            ->and($result->observacao)->toBe('Nota fiscal corrigida');
    });

    // verifica que delete delega a exclusao ao repository uma unica vez
    it('delega a exclusão ao repository', function () {
        $model = Purchase::factory()->make(['id' => 7]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('delete')
            ->with($model);

        $this->service->delete($model);
    });

    // verifica que index transforma os itens do paginator em arrays de PurchaseResponseDTO
    it('transforma os itens do paginator em arrays de PurchaseResponseDTO no index', function () {
        $model      = Purchase::factory()->make(['id' => 1, 'numero_nota' => 'NF-100', 'valor_total' => 250.50]);
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
            ->and($items[0]['numero_nota'])->toBe('NF-100')
            ->and($items[0]['valor_total'])->toBe(250.50);
    });
});
