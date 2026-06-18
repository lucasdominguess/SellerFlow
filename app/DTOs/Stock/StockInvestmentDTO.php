<?php

namespace App\DTOs\Stock;

class StockInvestmentDTO implements \JsonSerializable
{
    public function __construct(
        public readonly int $company_id,
        public readonly string $company_name,
        public readonly int $product_id,
        public readonly string $sku,
        public readonly string $product_name,
        public readonly int $saldo_atual,
        public readonly float $valor_investido,
        // Camadas FIFO que compõem o saldo atual: [{ qty, preco }, ...]
        public readonly array $composicao,
        public readonly bool $tem_unidade_sem_custo,
    ) {
    }

    public static function fromQueryResult(object $row): self
    {
        $composicao = $row->composicao;
        if (is_string($composicao)) {
            $composicao = json_decode($composicao, true) ?? [];
        }

        $composicao = array_map(
            fn ($camada) => [
                'qty'   => (int) $camada['qty'],
                'preco' => round((float) $camada['preco'], 2),
            ],
            $composicao
        );

        return new self(
            company_id: (int) $row->company_id,
            company_name: $row->company_name,
            product_id: (int) $row->product_id,
            sku: $row->sku,
            product_name: $row->product_name,
            saldo_atual: (int) $row->saldo_atual,
            valor_investido: round((float) $row->valor_investido, 2),
            composicao: $composicao,
            tem_unidade_sem_custo: (bool) (int) $row->tem_unidade_sem_custo,
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'company_id'            => $this->company_id,
            'company_name'          => $this->company_name,
            'product_id'            => $this->product_id,
            'sku'                   => $this->sku,
            'product_name'          => $this->product_name,
            'saldo_atual'           => $this->saldo_atual,
            'valor_investido'       => $this->valor_investido,
            'composicao'            => $this->composicao,
            'tem_unidade_sem_custo' => $this->tem_unidade_sem_custo,
        ];
    }
}
