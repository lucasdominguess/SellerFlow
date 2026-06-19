<?php

namespace App\DTOs\Finance;

use App\Classes\AuthContext;
use Illuminate\Support\Carbon;

class DashboardQueryDTO
{
    public function __construct(
        public readonly int $company_id,
        public readonly string $start_date,
        public readonly string $end_date,
    ) {}

    // company_id vem do JWT (AuthContext), nunca do cliente — escopo multi-tenant.
    // Sem datas no request, o período default é o mês corrente.
    public static function fromRequest(array $data): self
    {
        return new self(
            company_id: AuthContext::companyIds()->first(),
            start_date: $data['start_date'] ?? Carbon::now()->startOfMonth()->toDateString(),
            end_date:   $data['end_date'] ?? Carbon::now()->endOfMonth()->toDateString(),
        );
    }
}
