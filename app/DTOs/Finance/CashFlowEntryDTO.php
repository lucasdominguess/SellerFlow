<?php

namespace App\DTOs\Finance;

use Illuminate\Support\Carbon;

class CashFlowEntryDTO implements \JsonSerializable
{
    public function __construct(
        public readonly string $period,
        public readonly float $entradas,
        public readonly float $saidas,
        public readonly float $saldo,
        public readonly float $saldo_acumulado,
    ) {}

    // Projeta uma linha agregada da query. saldo_acumulado é o saldo corrente até este
    // período — calculado pelo Service ao percorrer os períodos em ordem.
    public static function fromQueryResult(object $row, float $saldoAcumulado): self
    {
        $entradas = round((float) $row->entradas, 2);
        $saidas   = round((float) $row->saidas, 2);

        return new self(
            period:          Carbon::parse($row->period)->toDateString(),
            entradas:        $entradas,
            saidas:          $saidas,
            saldo:           round($entradas - $saidas, 2),
            saldo_acumulado: round($saldoAcumulado, 2),
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'period'          => $this->period,
            'entradas'        => $this->entradas,
            'saidas'          => $this->saidas,
            'saldo'           => $this->saldo,
            'saldo_acumulado' => $this->saldo_acumulado,
        ];
    }
}
