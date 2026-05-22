<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DecimalPtBr implements Rule
{
    private int $totalDigits;
    private int $decimalDigits;

    public function __construct(int $totalDigits = 15, int $decimalDigits = 2)
    {
        $this->totalDigits = $totalDigits;
        $this->decimalDigits = $decimalDigits;
    }

    public function passes($attribute, $value): bool
    {
        if ($value === null || $value === '' || $value === '-') {
            return true;
        }

        $value = (string)$value;

        // Remove espaços em branco
        $value = trim($value);

        // Padrão PT-BR: 1.234.567,89 ou 1234567,89
        // Padrão EN: 1,234,567.89 ou 1234567.89
        // Também aceita ambos sem separadores: 1234567.89 ou 1234567,89

        // Remove separadores de milhar PT-BR (pontos)
        $normalized = str_replace('.', '', $value);

        // Substitui vírgula PT-BR por ponto EN
        $normalized = str_replace(',', '.', $normalized);

        // Valida formato numérico
        if (!is_numeric($normalized)) {
            return false;
        }

        $number = (float)$normalized;

        // Valida quantidade total de dígitos
        $parts = explode('.', $normalized);
        $integerPart = $parts[0];
        $decimalPart = $parts[1] ?? '';

        $totalDigits = strlen(str_replace('-', '', $integerPart . $decimalPart));

        if ($totalDigits > $this->totalDigits) {
            return false;
        }

        // Valida quantidade de casas decimais
        if (strlen($decimalPart) > $this->decimalDigits) {
            return false;
        }

        return $number >= 0;
    }

    public function message(): string
    {
        return "O campo :attribute deve ser um valor numérico em formato PT-BR (ex: 1.234.567,89) com até {$this->totalDigits} dígitos ({$this->decimalDigits} decimais).";
    }
}
