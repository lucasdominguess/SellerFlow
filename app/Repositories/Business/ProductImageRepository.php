<?php

namespace App\Repositories\Business;

use App\Contracts\Repositories\Business\ProductImageRepositoryInterface;
use App\Models\Business\ProductImage;

class ProductImageRepository implements ProductImageRepositoryInterface
{
    public function __construct(
        private ProductImage $productImageModel,
    ) {}

    public function createForProduct(int $productId, array $paths): void
    {
        // Continua a sequência: 0 no produto novo, max+1 ao anexar em produto existente.
        $maxPosition = $this->productImageModel->where('product_id', $productId)->max('position');
        $position = is_null($maxPosition) ? 0 : $maxPosition + 1;

        foreach ($paths as $path) {
            $this->productImageModel->create([
                'product_id' => $productId,
                'path'       => $path,
                'position'   => $position++,
            ]);
        }
    }
}
