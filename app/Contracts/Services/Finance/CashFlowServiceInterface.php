<?php

namespace App\Contracts\Services\Finance;

use App\DTOs\Finance\CashFlowQueryDTO;
use App\DTOs\Finance\CashFlowReportDTO;

interface CashFlowServiceInterface
{
    public function realized(CashFlowQueryDTO $dto): CashFlowReportDTO;
}
