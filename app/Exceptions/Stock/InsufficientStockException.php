<?php

namespace App\Exceptions\Stock;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(
        public readonly int $productId,
        public readonly int $available,
        public readonly int $requested,
    ) {
        parent::__construct(
            "Estoque insuficiente para o produto #{$productId}: disponível {$available}, solicitado {$requested}.",
            422
        );
    }
}
