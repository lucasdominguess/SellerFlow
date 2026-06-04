<?php

namespace App\Services\Sales;

use App\Contracts\Repositories\Sales\VendasRepositoryInterface;
use App\Contracts\Services\Sales\VendasServiceInterface;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\DTOs\Sales\VendasDTO;
use App\DTOs\Sales\VendasResponseDTO;
use App\Models\Sales\Venda;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class VendasService implements VendasServiceInterface
{
    public function __construct(
        private VendasRepositoryInterface $repository,
        private StockServiceInterface $stockService,
    ) {
    }

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return VendasResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(Venda $venda): VendasResponseDTO
    {
        $venda = $this->repository->show($venda);

        return VendasResponseDTO::fromModel($venda);
    }

    public function store(VendasDTO $dto): VendasResponseDTO
    {
        $venda = DB::transaction(function () use ($dto) {
            $data = $dto->toArray();
            // valor_liquido é derivado, não vem do cliente: bruto - taxa marketplace - frete
            $data['valor_liquido'] = $this->calcularValorLiquido(
                $data['valor_bruto'] ?? 0,
                $data['taxa_marketplace'] ?? 0,
                $data['valor_frete'] ?? 0,
            );

            $venda = $this->repository->store($data);


          $this->repository->storeItens($venda, $dto->venda_itens);
          $this->stockService->proccessItensSale($dto, $dto->venda_itens);

            return $venda;
        });

        return VendasResponseDTO::fromModel($venda);
    }

    public function update(Venda $venda, VendasDTO $dto): VendasResponseDTO
    {
        $data = $dto->toArray();
        // recalcula só quando algum componente do valor mudou, usando o valor atual como fallback
        if (isset($data['valor_bruto']) || isset($data['taxa_marketplace']) || isset($data['valor_frete'])) {
            $data['valor_liquido'] = $this->calcularValorLiquido(
                $data['valor_bruto'] ?? (float) $venda->valor_bruto,
                $data['taxa_marketplace'] ?? (float) $venda->taxa_marketplace,
                $data['valor_frete'] ?? (float) $venda->valor_frete,
            );
        }

        $venda = $this->repository->update($venda, $data);

        return VendasResponseDTO::fromModel($venda);
    }

    private function calcularValorLiquido(float $bruto, float $taxa, float $frete): float
    {
        return round($bruto - $taxa - $frete, 2);
    }

    public function delete(Venda $venda)
    {
        return $this->repository->delete($venda);

    }
}
