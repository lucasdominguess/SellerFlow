<?php

namespace App\Contracts\Services\Finance;

use App\DTOs\Finance\DashboardQueryDTO;
use App\DTOs\Finance\DashboardResponseDTO;

interface DashboardServiceInterface
{
    public function build(DashboardQueryDTO $dto): DashboardResponseDTO;
}
