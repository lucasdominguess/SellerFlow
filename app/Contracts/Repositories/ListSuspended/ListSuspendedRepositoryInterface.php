<?php

namespace App\Contracts\Repositories\ListSuspended;


interface ListSuspendedRepositoryInterface
{
    public function listCategoriaFinanceira(array $filters = []);
    public function listFornecedor(array $filters = []);
    public function listFormaPagamento(array $filters = []);
    public function listMarketplace(array $filters = []);
    public function listProduto(array $filters = []);
    public function listCompany(array $filters = []);

}
