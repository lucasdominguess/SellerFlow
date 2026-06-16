<?php

namespace App\Classes;

use App\DTOs\Business\ValidateProductDTO;
use App\Models\ListSuspended\MarketPlace;

class PriceCalculator
{
    public function calculate(ValidateProductDTO $dto, MarketPlace $marketplace): array
    {
        // decimais vêm como string do Eloquent (sem cast no model)
        $taxa_percentual = (float) $marketplace->taxa_percentual;
        $taxa_fixa = (float) $marketplace->taxa_fixa;

        // taxa do marketplace = percentual sobre a venda + taxa fixa por item
        $taxa_total = ($dto->price_sale * $taxa_percentual / 100) + $taxa_fixa;

        // lucro líquido em reais
        $lucro_liquido = $dto->price_sale
            - $dto->price_buy
            - $dto->cust_additional
            - $taxa_total;

        // margem de lucro sobre o preço de venda (ex: 20%)
        $lucro_percentual = $dto->price_sale > 0
            ? ($lucro_liquido / $dto->price_sale) * 100
            : 0.0;

        // ROAS de empate = faturamento necessário por real investido em ads para
        // o lucro da venda cobrir exatamente o custo do anúncio (price_sale / lucro).
        // Sem margem positiva é impossível empatar via ads, então retorna 0.
        $roas_empate = $lucro_liquido > 0
            ? $dto->price_sale / $lucro_liquido
            : 0.0;

        return [
            'price_sale' => round($dto->price_sale, 2),
            'price_buy' => round($dto->price_buy, 2),
            'cust_additional' => round($dto->cust_additional, 2),
            // snapshot da taxa usada (vira coluna no save)
            'fee_percent' => round($taxa_percentual, 2),
            'fee_fixed' => round($taxa_fixa, 2),
            // total derivado — não é coluna, só exibição
            'fee_total' => round($taxa_total, 2),
            'profit_amount' => round($lucro_liquido, 2),
            'profit_margin' => round($lucro_percentual, 2),
            'breakeven_roas' => round($roas_empate, 2),
        ];
    }
}
