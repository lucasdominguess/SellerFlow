<?php

namespace App\Docs\Swagger\Business;

use OpenApi\Attributes as OA;

// Documentação do CRUD de produtos (App\Http\Controllers\Business\ProductController).
// store/update usam multipart/form-data por causa do upload de imagens.
#[OA\Schema(
    schema: 'ProductImageResource',
    title: 'Imagem do produto',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 10),
        new OA\Property(property: 'url', type: 'string', example: '/storage/products/abc.jpg'),
        new OA\Property(property: 'position', type: 'integer', example: 0),
        new OA\Property(property: 'is_cover', type: 'boolean', description: 'true quando position = 0', example: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ProductResource',
    title: 'Produto',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'sku', type: 'string', example: 'CAM-AZUL-001'),
        new OA\Property(property: 'name', type: 'string', example: 'Camiseta Azul'),
        new OA\Property(property: 'marca', type: 'string', nullable: true, example: 'Marca X'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Camiseta 100% algodão'),
        new OA\Property(property: 'price_unit', type: 'number', format: 'float', example: 29.90),
        new OA\Property(property: 'price_box', type: 'number', format: 'float', example: 299.00),
        new OA\Property(property: 'status_id', type: 'integer', example: 1),
        new OA\Property(property: 'images', type: 'array', items: new OA\Items(ref: '#/components/schemas/ProductImageResource')),
        new OA\Property(property: 'fornecedor', nullable: true, ref: '#/components/schemas/SupplierResource'),
    ],
    type: 'object'
)]
class ProductDocs
{
    #[OA\Get(
        path: '/api/v1/product',
        operationId: 'listProducts',
        summary: 'Lista os produtos de forma paginada',
        tags: ['Product'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
            new OA\Parameter(name: 'name', in: 'query', required: false, description: 'Filtro opcional por nome', schema: new OA\Schema(type: 'string', example: 'Camiseta')),
            new OA\Parameter(name: 'sku', in: 'query', required: false, description: 'Filtro opcional por SKU', schema: new OA\Schema(type: 'string', example: 'CAM-AZUL-001')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Products recuperados com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Products recuperados com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ProductResource')),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
        ]
    )]
    public function index(): void
    {
    }

    #[OA\Get(
        path: '/api/v1/product/{product}',
        operationId: 'showProduct',
        summary: 'Exibe um produto específico',
        tags: ['Product'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, description: 'ID do produto', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Produto não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/product',
        operationId: 'createProduct',
        summary: 'Cria um novo produto (com upload opcional de imagens)',
        description: 'Enviar como multipart/form-data. O SKU é normalizado automaticamente. Até 10 imagens (jpg, jpeg, png, webp), máx 2MB cada.',
        tags: ['Product'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['sku', 'name', 'price_unit', 'price_box', 'status_id'],
                    properties: [
                        new OA\Property(property: 'sku', type: 'string', maxLength: 100, example: 'CAM-AZUL-001'),
                        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Camiseta Azul'),
                        new OA\Property(property: 'marca', type: 'string', maxLength: 100, nullable: true, example: 'Marca X'),
                        new OA\Property(property: 'description', type: 'string', maxLength: 255, nullable: true, example: 'Camiseta 100% algodão'),
                        new OA\Property(property: 'price_unit', type: 'number', format: 'float', minimum: 0, example: 29.90),
                        new OA\Property(property: 'price_box', type: 'number', format: 'float', minimum: 0, example: 299.00),
                        new OA\Property(property: 'status_id', type: 'integer', example: 1),
                        new OA\Property(property: 'fornecedor_id', type: 'integer', nullable: true, example: 1),
                        new OA\Property(
                            property: 'images[]',
                            type: 'array',
                            description: 'Até 10 imagens (jpg, jpeg, png, webp), máx 2MB cada',
                            items: new OA\Items(type: 'string', format: 'binary')
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Product criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product criado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function store(): void
    {
    }

    #[OA\Put(
        path: '/api/v1/product/{product}',
        operationId: 'updateProduct',
        summary: 'Atualiza um produto existente',
        description: 'Enviar como multipart/form-data. Para upload de imagens via PUT, o cliente pode precisar usar POST com _method=PUT (method spoofing do Laravel). Todos os campos são opcionais; envie apenas o que deseja alterar.',
        tags: ['Product'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, description: 'ID do produto', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'sku', type: 'string', maxLength: 100, example: 'CAM-AZUL-001'),
                        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Camiseta Azul'),
                        new OA\Property(property: 'marca', type: 'string', maxLength: 100, nullable: true, example: 'Marca X'),
                        new OA\Property(property: 'description', type: 'string', maxLength: 255, nullable: true, example: 'Camiseta 100% algodão'),
                        new OA\Property(property: 'price_unit', type: 'number', format: 'float', minimum: 0, example: 29.90),
                        new OA\Property(property: 'price_box', type: 'number', format: 'float', minimum: 0, example: 299.00),
                        new OA\Property(property: 'status_id', type: 'integer', example: 1),
                        new OA\Property(property: 'fornecedor_id', type: 'integer', nullable: true, example: 1),
                        new OA\Property(
                            property: 'images[]',
                            type: 'array',
                            description: 'Até 10 imagens (jpg, jpeg, png, webp), máx 2MB cada',
                            items: new OA\Items(type: 'string', format: 'binary')
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product atualizado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Produto não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/v1/product/{product}',
        operationId: 'deleteProduct',
        summary: 'Remove um produto',
        tags: ['Product'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, description: 'ID do produto', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product deletado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product deletado com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Produto não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(): void
    {
    }
}
