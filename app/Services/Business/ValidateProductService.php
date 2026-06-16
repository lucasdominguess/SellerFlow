<?php

namespace App\Services\Business;

use App\Classes\PriceCalculator;
use App\Contracts\Repositories\Business\ValidateProductRepositoryInterface;
use App\Contracts\Services\Business\ValidateProductServiceInterface;
use App\DTOs\Business\ValidateProductDTO;
use App\DTOs\Business\ValidateProductResponseDTO;
use App\Models\Business\ValidateProduct;
use App\Models\ListSuspended\MarketPlace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ValidateProductService implements ValidateProductServiceInterface
{
    public function __construct(
        private ValidateProductRepositoryInterface $repository,
        private PriceCalculator $priceCalculator
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return ValidateProductResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(ValidateProduct $validateProduct): ValidateProductResponseDTO
    {
        $validateProduct = $this->repository->show($validateProduct);

        return ValidateProductResponseDTO::fromModel($validateProduct);
    }

    public function store(ValidateProductDTO $dto): ValidateProductResponseDTO
    {
        // refaz a validação e reaproveita os campos calculados (fee_*, profit_*, breakeven_roas)
        $calculated = $this->validate($dto);

        $data = array_merge($dto->toArray(), $calculated->toArray());

        $validateProduct = $this->repository->store($data);

        return ValidateProductResponseDTO::fromModel($validateProduct);
    }

    public function update(ValidateProduct $validateProduct, ValidateProductDTO $dto): ValidateProductResponseDTO
    {
        $validateProduct = $this->repository->update($validateProduct, $dto->toArray());

        return ValidateProductResponseDTO::fromModel($validateProduct);
    }

    public function delete(ValidateProduct $validateProduct)
    {
        return $this->repository->delete($validateProduct);
    }

    public function validate(ValidateProductDTO $dto): ValidateProductResponseDTO
    {
        $marketplace = MarketPlace::find($dto->marketplace_id);

        $result = $this->priceCalculator->calculate($dto, $marketplace);

        return ValidateProductResponseDTO::fromArray($result);
    }
}
