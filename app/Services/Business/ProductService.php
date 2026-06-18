<?php

namespace App\Services\Business;

use App\Contracts\Repositories\Business\ProductImageRepositoryInterface;
use App\Contracts\Repositories\Business\ProductRepositoryInterface;
use App\Contracts\Services\Business\ProductServiceInterface;
use App\DTOs\Business\ProductDTO;
use App\DTOs\Business\ProductResponseDTO;
use App\Models\Business\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        private ProductRepositoryInterface $repository,
        private ProductImageRepositoryInterface $imageRepository,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return ProductResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(Product $product): ProductResponseDTO
    {
        $product = $this->repository->show($product);

        return ProductResponseDTO::fromModel($product);
    }

    public function store(ProductDTO $dto): ProductResponseDTO
    {
        // Grava os arquivos antes da transação; filesystem não é transacional.
        $paths = $this->storeUploadedImages($dto->images);

        try {
            $product = DB::transaction(function () use ($dto, $paths) {
                $product = $this->repository->store($dto->toArray());

                if ($paths) {
                    $this->imageRepository->createForProduct($product->id, $paths);
                }

                return $product;
            });
        } catch (\Throwable $e) {
            $this->deleteStoredImages($paths);
            throw $e;
        }

        return ProductResponseDTO::fromModel($this->repository->show($product));
    }

    public function update(Product $product, ProductDTO $dto): ProductResponseDTO
    {
        $paths = $this->storeUploadedImages($dto->images);

        try {
            $product = DB::transaction(function () use ($product, $dto, $paths) {
                $product = $this->repository->update($product, $dto->toArray());

                if ($paths) {
                    $this->imageRepository->createForProduct($product->id, $paths);
                }

                return $product;
            });
        } catch (\Throwable $e) {
            $this->deleteStoredImages($paths);
            throw $e;
        }

        return ProductResponseDTO::fromModel($this->repository->show($product));
    }

    public function delete(Product $product)
    {
        // Coleta os paths antes de remover; as linhas em product_images caem por cascade na FK.
        $paths = $product->images()->pluck('path')->all();

        $result = $this->repository->delete($product);

        // Remove os arquivos do disco só após o delete no banco (fonte da verdade).
        $this->deleteStoredImages($paths);

        return $result;
    }

    // Move os uploads para o disco e retorna os paths relativos (vazio se não houver imagens).
    private function storeUploadedImages(array $images): array
    {
        return array_map(
            fn ($image) => $image->store('products/images', 'public'),
            $images
        );
    }

    private function deleteStoredImages(array $paths): void
    {
        if ($paths) {
            Storage::disk('public')->delete($paths);
        }
    }
}
