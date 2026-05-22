<?php

namespace App\Services\ListSuspended;

use App\Contracts\Repositories\ListSuspended\ListSuspendedRepositoryInterface;
use App\Contracts\Services\ListSuspended\ListSuspendedServiceInterface;

class ListSuspendedService implements ListSuspendedServiceInterface
{
    public function __construct(
        protected ListSuspendedRepositoryInterface $repository,
    ) {}

    public function listCategoriaFinanceira(array $filters = [])
    {
        $data = $this->repository->listCategoriaFinanceira($filters);
        return $data;
    }

    public function listFornecedor(array $filters = [])
    {
        $data = $this->repository->listFornecedor($filters);
        return $data;
    }

    public function listFormaPagamento(array $filters = [])
    {
        $data = $this->repository->listFormaPagamento($filters);
        return $data;
    }

    public function listMarketplace(array $filters = [])
    {
        $data = $this->repository->listMarketplace($filters);
        return $data;
    }

    public function listProduto(array $filters = [])
    {
        $data = $this->repository->listProduto($filters);
        return $data;
    }
    public function listCompany(array $filters = [])
    {
        $data = $this->repository->listCompany($filters);
        return $data;
    }

}
