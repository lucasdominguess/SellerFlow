<?php

namespace App\DTOs\Finance;

class DashboardResponseDTO implements \JsonSerializable
{
    public function __construct(
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly array $vendas,
        public readonly array $compras,
        public readonly array $a_receber,
        public readonly array $a_pagar,
        public readonly array $estoque,
        // lista de ['product_id','sku','name','quantidade']
        public readonly array $top_produtos,
    ) {}

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'periodo' => [
                'inicio' => $this->start_date,
                'fim'    => $this->end_date,
            ],
            'vendas'  => $this->vendas,
            'compras' => $this->compras,
            'financeiro' => [
                'a_receber' => $this->a_receber,
                'a_pagar'   => $this->a_pagar,
            ],
            'estoque'      => $this->estoque,
            'top_produtos' => $this->top_produtos,
        ];
    }
}
