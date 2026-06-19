<?php

namespace App\Http\Controllers\Finance;

use App\Classes\ApiResponse;
use App\Contracts\Services\Finance\DashboardServiceInterface;
use App\DTOs\Finance\DashboardQueryDTO;
use App\Http\Requests\Finance\DashboardRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardServiceInterface $service,
    ) {}

    public function index(DashboardRequest $request): JsonResponse
    {
        $dashboard = $this->service->build(DashboardQueryDTO::fromRequest($request->validated()));

        return ApiResponse::success($dashboard, 'Dashboard recuperado com sucesso');
    }
}
