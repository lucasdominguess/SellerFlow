<?php

namespace App\Contracts\Services\Business;

use App\DTOs\Business\ValidateProductDTO;
use App\DTOs\Business\ValidateProductResponseDTO;
use App\Models\Business\ValidateProduct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ValidateProductServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(ValidateProduct $validateProduct): ValidateProductResponseDTO;

    public function store(ValidateProductDTO $dto): ValidateProductResponseDTO;

    public function update(ValidateProduct $validateProduct, ValidateProductDTO $dto): ValidateProductResponseDTO;

    public function delete(ValidateProduct $validateProduct);

    public function validate(ValidateProductDTO $dto): ValidateProductResponseDTO;
}
