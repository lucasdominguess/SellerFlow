<?php

namespace App\DTOs\Finance;

class CashFlowReportDTO implements \JsonSerializable
{
    public function __construct(
        public readonly string $granularity,
        public readonly string $start_date,
        public readonly string $end_date,
        // ['total_entradas' => float, 'total_saidas' => float, 'saldo' => float]
        public readonly array $summary,
        // CashFlowEntryDTO[]
        public readonly array $periods,
    ) {}

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'granularity' => $this->granularity,
            'start_date'  => $this->start_date,
            'end_date'    => $this->end_date,
            'summary'     => $this->summary,
            'periods'     => array_map(fn (CashFlowEntryDTO $entry) => $entry->toArray(), $this->periods),
        ];
    }
}
