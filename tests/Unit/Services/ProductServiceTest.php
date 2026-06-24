<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\Business\ProductImageRepositoryInterface;
use App\Contracts\Repositories\Business\ProductRepositoryInterface;
use App\DTOs\Business\ProductDTO;
use App\Services\Business\ProductService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('ProductService', function () {

    it('remove os arquivos de imagem já gravados quando a transação falha', function () {
        Storage::fake('public');

        $repositoryMock = $this->createMock(ProductRepositoryInterface::class);
        // a persistência falha DENTRO da transação, depois das imagens já terem ido pro disco
        $repositoryMock->method('store')->willThrowException(new \RuntimeException('falha no banco'));

        $imageRepositoryMock = $this->createMock(ProductImageRepositoryInterface::class);

        $service = new ProductService($repositoryMock, $imageRepositoryMock);

        $dto = ProductDTO::fromRequest([
            'sku'        => 'CAPA',
            'name'       => 'Capa de Celular',
            'price_unit' => 10.00,
            'price_box'  => 90.00,
            'status_id'  => 1,
            'images'     => [UploadedFile::fake()->image('a.jpg')],
        ]);

        expect(fn () => $service->store($dto))->toThrow(\RuntimeException::class);

        // o arquivo gravado antes da transação foi removido no rollback (nada órfão fica no disco)
        expect(Storage::disk('public')->allFiles('products/images'))->toBeEmpty();
    });
});
