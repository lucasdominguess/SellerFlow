<?php

namespace Tests\Unit\Classes;

use App\Classes\PriceCalculator;
use App\DTOs\Business\ValidateProductDTO;
use App\Models\ListSuspended\MarketPlace;

// DTO de validação a partir de preços simples (os demais campos não afetam o cálculo).
function pricingDto(float $sale, float $buy, float $cust = 0): ValidateProductDTO
{
    return ValidateProductDTO::fromRequest([
        'price_sale'      => $sale,
        'price_buy'       => $buy,
        'cust_additional' => $cust,
        'marketplace_id'  => 1,
    ]);
}

describe('PriceCalculator', function () {

    beforeEach(function () {
        $this->calculator = new PriceCalculator();
        // marketplace em memória (sem banco): taxa 20% + R$4 fixos
        $this->marketplace = new MarketPlace(['taxa_percentual' => 20, 'taxa_fixa' => 4]);
    });

    it('calcula taxa, lucro, margem e roas de empate para um produto lucrativo', function () {
        // venda 100, compra 50, custo 0; taxa = 100*20% + 4 = 24
        $result = $this->calculator->calculate(pricingDto(100, 50, 0), $this->marketplace);

        expect($result['fee_total'])->toBe(24.0)         // 20 + 4
            ->and($result['profit_amount'])->toBe(26.0)   // 100 - 50 - 0 - 24
            ->and($result['profit_margin'])->toBe(26.0)   // 26 / 100 * 100
            ->and($result['breakeven_roas'])->toBe(3.85); // 100 / 26
    });

    it('retorna roas de empate 0 quando não há lucro (margem negativa)', function () {
        // venda 50, compra 50, custo 10; taxa = 50*20% + 4 = 14; lucro = -24
        $result = $this->calculator->calculate(pricingDto(50, 50, 10), $this->marketplace);

        expect($result['profit_amount'])->toBe(-24.0)
            ->and($result['breakeven_roas'])->toBe(0.0); // sem margem positiva, não dá pra empatar via ads
    });

    it('retorna margem 0 quando o preço de venda é 0 (evita divisão por zero)', function () {
        $result = $this->calculator->calculate(pricingDto(0, 0, 0), $this->marketplace);

        expect($result['profit_margin'])->toBe(0.0)
            ->and($result['breakeven_roas'])->toBe(0.0);
    });

    it('faz snapshot da taxa do marketplace usada no cálculo', function () {
        $result = $this->calculator->calculate(pricingDto(100, 50), $this->marketplace);

        expect($result['fee_percent'])->toBe(20.0)
            ->and($result['fee_fixed'])->toBe(4.0);
    });
});
