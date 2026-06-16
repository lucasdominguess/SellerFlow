<?php

namespace App\Classes;

class Sku
{

    public static function generate(string $prefix, ?int $length=3): string
    {
        $normalizedPrefix = self::normalizePrefix($prefix);
        $token = self::randomToken($length);
        $timestamp = self::getTimestamp();
        return "{$normalizedPrefix}-{$timestamp}-{$token}";
    }
    private static function normalizePrefix(string $prefix): string
    {
        $clean = preg_replace('/[^A-Z0-9\-]/', '-', strtoupper($prefix)) ?? '';

        if ($clean === '') {
            throw new \InvalidArgumentException('Prefix must contain at least one alphanumeric character.');
        }

        return $clean;
    }
      private static function randomToken(int $length): string
    {
        // Crockford-friendly: sem caracteres ambíguos (I, O, 0, 1)
        $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $token;
    }
    private static function getTimestamp(): int
    {
        return time();
    }
    private static function abbreviateProductName(string $name, int $prefixLength = 3): string
{
    // $stopWords = ['de', 'da', 'do', 'das', 'dos', 'e', 'com', 'para', 'em', 'a', 'o'];
    $stopWords = ['DE', 'DA', 'DO', 'DAS', 'DOS', 'E', 'COM', 'PARA', 'EM', 'A', 'O'];
    $words = explode(' ', mb_strtoupper(trim($name)));

    // Remove stopwords
    $significant = array_values(array_filter(
        $words,
        fn(string $word) => ! in_array($word, $stopWords, strict: true)
    ));

    if (empty($significant)) {
        return mb_substr($name, 0, $prefixLength);
    }

    $first = mb_substr($significant[0], 0, $prefixLength); // "rep"
    $last  = end($significant);                             // "argan"

    if ($first === $last) {
        return $first; // palavra única
    }

    return "{$first}-{$last}";
}

}

