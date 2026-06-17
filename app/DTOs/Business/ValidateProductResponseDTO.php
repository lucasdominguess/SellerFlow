<?php

namespace App\DTOs\Business;

use App\Models\Business\ValidateProduct;

class ValidateProductResponseDTO implements \JsonSerializable
{
    public function __construct(
        public readonly float $price_sale,
        public readonly float $price_buy,
        public readonly float $cust_additional,
        public readonly float $fee_percent,
        public readonly float $fee_fixed,
        public readonly float $fee_total,
        public readonly float $profit_amount,
        public readonly float $profit_margin,
        public readonly float $breakeven_roas,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            price_sale: (float) $data['price_sale'],
            price_buy: (float) $data['price_buy'],
            cust_additional: (float) $data['cust_additional'],
            fee_percent: (float) $data['fee_percent'],
            fee_fixed: (float) $data['fee_fixed'],
            fee_total: (float) $data['fee_total'],
            profit_amount: (float) $data['profit_amount'],
            profit_margin: (float) $data['profit_margin'],
            breakeven_roas: (float) $data['breakeven_roas'],
        );
    }

    public static function fromModel(ValidateProduct $model): self
    {
        // fee_total não é coluna — derivado do snapshot (fee_percent + fee_fixed)
        $fee_total = ((float) $model->price_sale * (float) $model->fee_percent / 100)
            + (float) $model->fee_fixed;

        return new self(
            price_sale: (float) $model->price_sale,
            price_buy: (float) $model->price_buy,
            cust_additional: (float) $model->cust_additional,
            fee_percent: (float) $model->fee_percent,
            fee_fixed: (float) $model->fee_fixed,
            fee_total: round($fee_total, 2),
            profit_amount: (float) $model->profit_amount,
            profit_margin: (float) $model->profit_margin,
            breakeven_roas: (float) $model->breakeven_roas,
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'price_sale' => $this->price_sale,
            'price_buy' => $this->price_buy,
            'cust_additional' => $this->cust_additional,
            'fee_percent' => $this->fee_percent,
            'fee_fixed' => $this->fee_fixed,
            'fee_total' => $this->fee_total,
            'profit_amount' => $this->profit_amount,
            'profit_margin' => $this->profit_margin,
            'breakeven_roas' => $this->breakeven_roas,
        ];
    }
}
