<?php

namespace App\Contracts\Services\ListSuspended;


interface ListSuspendedServiceInterface
{

    public function listCategoriaFinanceira(array $filters=[]);
    public function listFornecedor(array $filters=[]);
    public function listFormaPagamento(array $filters=[]);
    public function listMarketplace(array $filters=[]);
    public function listProduto(array $filters=[]);
    public function listCompany(array $filters=[]);
}
