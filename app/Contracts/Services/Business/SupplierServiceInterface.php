<?php

namespace App\Contracts\Services\Business;

use App\DTOs\Business\SupplierDTO;
use App\DTOs\Business\SupplierResponseDTO;
use App\Models\Business\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SupplierServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Supplier $supplier): SupplierResponseDTO;

    public function store(SupplierDTO $dto): SupplierResponseDTO;

    public function update(Supplier $supplier, SupplierDTO $dto): SupplierResponseDTO;

    public function delete(Supplier $supplier);
}
