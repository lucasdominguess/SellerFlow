<?php

namespace App\Contracts\Services\Business;

use App\DTOs\Business\FornecedorDTO;
use App\DTOs\Business\FornecedorResponseDTO;
use App\Models\Business\Fornecedor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FornecedorServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Fornecedor $fornecedor): FornecedorResponseDTO;

    public function store(FornecedorDTO $dto): FornecedorResponseDTO;

    public function update(Fornecedor $fornecedor, FornecedorDTO $dto): FornecedorResponseDTO;

    public function delete(Fornecedor $fornecedor);
}
