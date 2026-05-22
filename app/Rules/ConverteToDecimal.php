<?php

namespace App\Rules;


class ConverteToDecimal{

    public function convertToDecimal(?string $value): ?float
    {
        if (!$value || $value === '-' || $value === '') {
            return null;
        }
        // Remove pontos e substitui vírgula por ponto
        $value = str_replace(['.', ','], ['', '.'], $value);
        return (float) $value;
    }
}
