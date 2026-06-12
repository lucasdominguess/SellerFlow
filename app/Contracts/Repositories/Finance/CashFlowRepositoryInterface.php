<?php

namespace App\Contracts\Repositories\Finance;

use App\DTOs\Finance\CashFlowQueryDTO;
use Illuminate\Support\Collection;

interface CashFlowRepositoryInterface
{
    // Retorna as linhas agregadas por período: { period, entradas, saidas }, ordenadas por período.
    public function realized(CashFlowQueryDTO $dto): Collection;
}
