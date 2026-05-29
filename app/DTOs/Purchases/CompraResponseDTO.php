<?php

namespace App\DTOs\Purchases;

use App\Models\Purchases\Compra;

class CompraResponseDTO implements \JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $company_id,
        public readonly ?int $store_id,
        public readonly ?int $fornecedor_id,
        public readonly ?int $user_id,
        public readonly ?int $forma_pagamento_id,
        public readonly ?int $status_id,
        public readonly ?string $numero_nota,
        public readonly ?string $data_compra,
        public readonly ?float $valor_total,
        public readonly ?int $numero_parcelas,
        public readonly ?string $observacao,
        public readonly array $itens = [],
    ) {}

    public static function fromModel(Compra $model): self
    {
        return new self(
            id:                $model->id,
            company_id:        $model->company_id,
            store_id:          $model->store_id,
            fornecedor_id:     $model->fornecedor_id,
            user_id:           $model->user_id,
            forma_pagamento_id: $model->forma_pagamento_id,
            status_id:         $model->status_id,
            numero_nota:       $model->numero_nota,
            data_compra:       $model->data_compra?->toDateString(),
            valor_total:       (float) $model->valor_total,
            numero_parcelas:   $model->numero_parcelas,
            observacao:        $model->observacao,
            // Só serializa itens se a relação foi carregada (evita lazy load; index retorna [])
            itens: $model->relationLoaded('itens')
                ? $model->itens->map(fn($item) => CompraItemResponseDTO::fromModel($item)->toArray())->toArray()
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
            'id'                => $this->id,
            'company_id'        => $this->company_id,
            'store_id'          => $this->store_id,
            'fornecedor_id'     => $this->fornecedor_id,
            'user_id'           => $this->user_id,
            'forma_pagamento_id' => $this->forma_pagamento_id,
            'status_id'         => $this->status_id,
            'numero_nota'       => $this->numero_nota,
            'data_compra'       => $this->data_compra,
            'valor_total'       => $this->valor_total,
            'numero_parcelas'   => $this->numero_parcelas,
            'observacao'        => $this->observacao,
            'itens'             => $this->itens,
        ];
    }
}
