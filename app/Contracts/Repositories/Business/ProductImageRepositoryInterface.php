<?php

namespace App\Contracts\Repositories\Business;

interface ProductImageRepositoryInterface
{
    // Cria as linhas em product_images para os paths informados, continuando a sequência de position.
    public function createForProduct(int $productId, array $paths): void;
}
