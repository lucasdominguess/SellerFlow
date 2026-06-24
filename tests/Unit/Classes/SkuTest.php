<?php

namespace Tests\Unit\Classes;

use App\Classes\Sku;

describe('Sku::generate', function () {

    it('gera um sku no formato PREFIXO-timestamp-token', function () {
        $sku = Sku::generate('capa');

        // prefixo normalizado em maiúsculas
        expect($sku)->toStartWith('CAPA-');

        $parts = explode('-', $sku);
        expect($parts)->toHaveCount(3)
            // token com o tamanho padrão (3)
            ->and(strlen($parts[2]))->toBe(3);
    });

    it('respeita o tamanho do token informado', function () {
        $sku   = Sku::generate('xyz', 5);
        $parts = explode('-', $sku);

        expect(strlen($parts[2]))->toBe(5);
    });

    it('normaliza caracteres não alfanuméricos do prefixo', function () {
        $sku = Sku::generate('capa@celular');

        // @ vira '-' (não permanece no resultado)
        expect($sku)->toStartWith('CAPA-CELULAR-')
            ->and($sku)->not->toContain('@');
    });

    it('usa um alfabeto sem caracteres ambíguos no token', function () {
        $token = explode('-', Sku::generate('abc'))[2];

        // sem I, O, 0, 1 (Crockford-friendly)
        expect($token)->not->toMatch('/[IO01]/');
    });

    it('lança exceção para prefixo vazio', function () {
        expect(fn () => Sku::generate(''))->toThrow(\InvalidArgumentException::class);
    });
});
