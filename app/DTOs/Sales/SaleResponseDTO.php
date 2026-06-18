<?php

namespace App\DTOs\Sales;

use App\Models\Sales\Sale;
use App\DTOs\Sales\SaleItemResponseDTO;

class SaleResponseDTO implements \JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly int $company_id,
        public readonly int $store_id,
        public readonly int $market_place_id,
        public readonly int $user_id,
        public readonly ?string $status,
        public readonly string $numero_pedido,
        public readonly ?string $data_venda,
        public readonly float $valor_bruto,
        public readonly float $taxa_marketplace,
        public readonly float $valor_frete,
        public readonly float $valor_liquido,
        public readonly ?string $data_previsao_repasse,
        public readonly ?string $observacao,
        public readonly array $itens = [],
    ) {}

    public static function fromModel(Sale $model): self
    {
        return new self(
            id: $model->id,
            company_id: $model->company_id,
            store_id: $model->store_id,
            market_place_id: $model->market_place_id,
            user_id: $model->user_id,
            status: $model->status?->value,
            numero_pedido: $model->numero_pedido,
            data_venda: $model->data_venda?->toDateString(),
            valor_bruto: (float) $model->valor_bruto,
            taxa_marketplace: (float) $model->taxa_marketplace,
            valor_frete: (float) $model->valor_frete,
            valor_liquido: (float) $model->valor_liquido,
            data_previsao_repasse: $model->data_previsao_repasse?->toDateString(),
            observacao: $model->observacao,
            // Só serializa itens se a relação foi carregada (evita lazy load; index retorna [])
            itens: $model->relationLoaded('itens')
                ? $model->itens->map(fn($item) => SaleItemResponseDTO::fromModel($item)->toArray())->toArray()
                : [],
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'id'                    => $this->id,
            'company_id'            => $this->company_id,
            'store_id'              => $this->store_id,
            'market_place_id'       => $this->market_place_id,
            'user_id'               => $this->user_id,
            'status'                => $this->status,
            'numero_pedido'         => $this->numero_pedido,
            'data_venda'            => $this->data_venda,
            'valor_bruto'           => $this->valor_bruto,
            'taxa_marketplace'      => $this->taxa_marketplace,
            'valor_frete'           => $this->valor_frete,
            'valor_liquido'         => $this->valor_liquido,
            'data_previsao_repasse' => $this->data_previsao_repasse,
            'observacao'            => $this->observacao,
            'itens'                 => $this->itens,
        ];
    }
}
