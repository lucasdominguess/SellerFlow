<?php

namespace App\Repositories\ListSuspended;

use App\Contracts\Repositories\ListSuspended\ListSuspendedRepositoryInterface;
use App\Models\ListSuspended\CategoriaFinanceira;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\FormaPagamento;
use App\Models\ListSuspended\MarketPlace;

class ListSuspendedRepository implements ListSuspendedRepositoryInterface
{
    public function __construct(
        private CategoriaFinanceira $categoriaFinanceiraModel,
        private FormaPagamento $formaPagamentoModel,
        private MarketPlace $marketplaceModel,
        private Company $companyModel,
        // private Fornecedor $fornecedorModel,
        // private Produto $produtoModel,
    ) {}
    public function listCategoriaFinanceira(array $filters = [])
    {
        $query = $this->categoriaFinanceiraModel->query();

        if(empty($filters)) return $query->orderByDesc('id')->get();

        if(!empty($filters['name'])){
            $query->where('name','ilike', '%' . $filters['name'] . '%');
        }

        return $query->orderByDesc('id')->get();

    }
    public function listFornecedor(array $filters = [])
    {
        $query = $this->fornecedorModel->query();

        if(empty($filters)) return $query->orderByDesc('id')->get();

        if(!empty($filters['name'])){
            $query->where('name','ilike', '%' . $filters['name'] . '%');
        }
        if(!empty($filters['status_id'])){
            $query->where('status_id', $filters['status_id']);
        }
        return $query->orderByDesc('id')->get();
    }
    public function listFormaPagamento(array $filters = [])
    {
        $query = $this->formaPagamentoModel->query();

        if(empty($filters)) return $query->orderByDesc('id')->get();

        if(!empty($filters['name'])){
            $query->where('name','ilike', '%' . $filters['name'] . '%');
        }

        return $query->orderByDesc('id')->get();

    }
    public function listMarketplace(array $filters = [])
    {
        $query = $this->marketplaceModel->query();

        if(empty($filters)) return $query->orderByDesc('id')->get();

        if(!empty($filters['name'])){
            $query->where('name','ilike', '%' . $filters['name'] . '%');
        }
        if(!empty($filters['status_id'])){
            $query->where('status_id', $filters['status_id']);
        }
        return $query->orderByDesc('id')->get();
    }
    public function listProduto(array $filters = [])
    {
        $query = $this->produtoModel->query();

        if(empty($filters)) return $query->orderByDesc('id')->get();

        if(!empty($filters['name'])){
            $query->where('name','ilike', '%' . $filters['name'] . '%');
        }
        if(!empty($filters['status_id'])){
            $query->where('status_id', $filters['status_id']);
        }
        return $query->orderByDesc('id')->get();
    }
    public function listCompany(array $filters = [])
    {
        $query = $this->companyModel->query();

        if(empty($filters)) return $query->orderByDesc('id')->get();

        if(!empty($filters['name'])){
            $query->where('name','ilike', '%' . $filters['name'] . '%');
        }
        if(!empty($filters['status_id'])){
            $query->where('status_id', $filters['status_id']);
        }
        return $query->orderByDesc('id')->get();
    }

}
