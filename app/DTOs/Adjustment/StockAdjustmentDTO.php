<?php

namespace App\DTOs\Adjustment;

class StockAdjustmentDTO
{
    public function __construct(
        public readonly ?int $company_id,
        public readonly ?int $user_id,
        // StockAdjustmentItemDTO[] — cada item vira uma linha própria em stock_adjustments
        public readonly array $itens = [],
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            company_id: $data['company_id'] ?? null,
            user_id: $data['user_id'] ?? null,
            itens: array_map(
                fn(array $item) => StockAdjustmentItemDTO::fromArray($item),
                $data['itens'] ?? []
            ),
        );
    }

    // itens não entra aqui: cada item é persistido como sua própria linha pelo Repository
    public function toArray(): array
    {
        return array_filter([
            'company_id' => $this->company_id,
            'user_id' => $this->user_id,
        ], fn($value) => !is_null($value));
    }
}
