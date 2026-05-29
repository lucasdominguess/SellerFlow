<?php

namespace App\Services\Purchases;

use App\Contracts\Repositories\Purchases\CompraRepositoryInterface;
use App\Contracts\Services\Purchases\CompraServiceInterface;
use App\DTOs\Purchases\CompraDTO;
use App\DTOs\Purchases\CompraResponseDTO;
use App\Models\Purchases\Compra;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompraService implements CompraServiceInterface
{
    public function __construct(
        private CompraRepositoryInterface $repository,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return CompraResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(Compra $compra): CompraResponseDTO
    {
        $compra = $this->repository->show($compra);

        return CompraResponseDTO::fromModel($compra);
    }

    public function store(CompraDTO $dto): CompraResponseDTO
    {
        $compra = DB::transaction(function () use ($dto) {
            $data = $dto->toArray();
            // valor_total é recalculado dos itens para garantir consistência com o que foi salvo
            $data['valor_total'] = $this->calcularValorTotal($dto->itens);

            $compra = $this->repository->store($data);

            // storeItens cria os itens, calcula valor_total por item e retorna compra com relação carregada
            return $this->repository->storeItens($compra, $dto->itens);
        });

        return CompraResponseDTO::fromModel($compra);
    }

    public function update(Compra $compra, CompraDTO $dto): CompraResponseDTO
    {
        $compra = $this->repository->update($compra, $dto->toArray());

        return CompraResponseDTO::fromModel($compra);
    }

    public function delete(Compra $compra)
    {
        return $this->repository->delete($compra);
    }

    // valor_total da compra é a soma de (quantidade * valor_unitario) de cada item
    private function calcularValorTotal(array $itens): float
    {
        return round(array_reduce($itens, function (float $carry, $item) {
            return $carry + ($item->quantidade * $item->valor_unitario);
        }, 0.0), 2);
    }
}
