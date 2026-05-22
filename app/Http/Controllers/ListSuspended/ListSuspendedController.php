<?php

namespace App\Http\Controllers\ListSuspended;

use App\Classes\ApiResponse;
use App\Contracts\Services\ListSuspended\ListSuspendedServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListSuspendedController extends Controller
{
    public function __construct(
        private ListSuspendedServiceInterface $service,
    ) {
    }



    public function list(Request $request): JsonResponse
    {
        $params = $request->input('params') ?? null;
        $filters = $request->input('filters', []) ?? null;

        $data = match ($params) {
            'categoria-financeira' => $this->service->listCategoriaFinanceira($filters),
            'fornecedor' => $this->service->listFornecedor($filters),
            'forma-pagamento' => $this->service->listFormaPagamento($filters),
            'marketplace' => $this->service->listMarketplace($filters),
            'produto' => $this->service->listProduto($filters),
            'company' => $this->service->listCompany($filters),
            default => throw new \InvalidArgumentException('Parâmetro inválido'),
        };

        return ApiResponse::success($data, 'Lista de itens retornada com sucesso');

    }
}
