<?php

namespace App\Http\Controllers\Finance;

use App\Classes\ApiResponse;
use App\Contracts\Services\Finance\CashFlowServiceInterface;
use App\DTOs\Finance\CashFlowQueryDTO;
use App\Http\Requests\Finance\CashFlowRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CashFlowController extends Controller
{
    public function __construct(
        private CashFlowServiceInterface $service,
    ) {}

    public function index(CashFlowRequest $request): JsonResponse
    {
        $report = $this->service->realized(CashFlowQueryDTO::fromRequest($request->validated()));

        return ApiResponse::success($report, 'Fluxo de caixa recuperado com sucesso');
    }
}
