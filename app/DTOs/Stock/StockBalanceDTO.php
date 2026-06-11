<?php

namespace App\DTOs\Stock;

class StockBalanceDTO implements \JsonSerializable
{
    public function __construct(
        public readonly int $company_id,
        public readonly string $company_name,
        public readonly int $product_id,
        public readonly string $sku,
        public readonly string $product_name,
        public readonly ?string $last_adjustment_user,
        public readonly int $total_entradas,
        public readonly int $total_saidas,
        public readonly int $total_ajustes_positivos,
        public readonly int $total_ajustes_negativos,
        public readonly int $saldo_atual,
    ) {
    }

    public static function fromQueryResult(object $row): self
    {
        return new self(
            company_id: (int) $row->company_id,
            company_name: $row->company_name,
            product_id: (int) $row->product_id,
            sku: $row->sku,
            product_name: $row->product_name,
            last_adjustment_user: $row->last_adjustment_user,
            total_entradas: (int) $row->total_entradas,
            total_saidas: (int) $row->total_saidas,
            total_ajustes_positivos: (int) $row->total_ajustes_positivos,
            total_ajustes_negativos: (int) $row->total_ajustes_negativos,
            saldo_atual: (int) $row->saldo_atual,
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'company_id' => $this->company_id,
            'company_name' => $this->company_name,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'product_name' => $this->product_name,
            'last_adjustment_user' => $this->last_adjustment_user,
            'total_entradas' => $this->total_entradas,
            'total_saidas' => $this->total_saidas,
            'total_ajustes_positivos' => $this->total_ajustes_positivos,
            'total_ajustes_negativos' => $this->total_ajustes_negativos,
            'saldo_atual' => $this->saldo_atual,
        ];
    }
}
