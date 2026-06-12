<?php

namespace App\DTOs\Finance;

use App\Classes\AuthContext;

class CashFlowQueryDTO
{
    public function __construct(
        public readonly int $company_id,
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly string $granularity,
    ) {}

    // company_id vem do JWT (AuthContext), nunca do cliente — escopo multi-tenant.
    public static function fromRequest(array $data): self
    {
        return new self(
            company_id:  AuthContext::companyIds()->first(),
            start_date:  $data['start_date'],
            end_date:    $data['end_date'],
            granularity: $data['granularity'] ?? 'month',
        );
    }
}
