<?php

namespace App\Docs\Swagger\Account;

use OpenApi\Attributes as OA;

// Documentação do CRUD de lojas (App\Http\Controllers\Accout\StoreController).
// Define também os schemas reutilizáveis MarketplaceResource e CompanyResource (DTOs aninhados).
#[OA\Schema(
    schema: 'MarketplaceResource',
    title: 'Marketplace',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Shopee'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Marketplace asiático'),
        new OA\Property(property: 'taxa_percentual', type: 'number', format: 'float', nullable: true, example: 12.0),
        new OA\Property(property: 'taxa_fixa', type: 'number', format: 'float', nullable: true, example: 4.0),
        new OA\Property(property: 'status_id', type: 'integer', example: 1),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'CompanyResource',
    title: 'Empresa',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Loja do João LTDA'),
        new OA\Property(property: 'cnpj', type: 'string', nullable: true, example: '12.345.678/0001-90'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Loja de eletrônicos'),
        new OA\Property(property: 'status_id', type: 'integer', example: 1),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'StoreResource',
    title: 'Loja',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Loja Principal'),
        new OA\Property(property: 'email', type: 'string', format: 'email', nullable: true, example: 'loja@sellerflow.com'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Loja de eletrônicos'),
        new OA\Property(property: 'status_id', type: 'integer', example: 1),
        new OA\Property(property: 'marketplace', ref: '#/components/schemas/MarketplaceResource'),
        new OA\Property(property: 'company', nullable: true, ref: '#/components/schemas/CompanyResource'),
    ],
    type: 'object'
)]
class StoreDocs
{
    #[OA\Get(
        path: '/api/v1/store',
        operationId: 'listStores',
        summary: 'Lista as lojas de forma paginada',
        tags: ['Store'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
            new OA\Parameter(name: 'name', in: 'query', required: false, description: 'Filtro opcional por nome', schema: new OA\Schema(type: 'string', example: 'Loja')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Stores recuperados com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Stores recuperados com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/StoreResource')),
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
        path: '/api/v1/store/{store}',
        operationId: 'showStore',
        summary: 'Exibe uma loja específica',
        tags: ['Store'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'store', in: 'path', required: true, description: 'ID da loja', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Store recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Store recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/StoreResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Loja não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/store',
        operationId: 'createStore',
        summary: 'Cria uma nova loja',
        tags: ['Store'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'status_id', 'marketplace_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Loja Principal'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, nullable: true, example: 'loja@sellerflow.com'),
                    new OA\Property(property: 'description', type: 'string', maxLength: 255, nullable: true, example: 'Loja de eletrônicos'),
                    new OA\Property(property: 'status_id', type: 'integer', example: 1),
                    new OA\Property(property: 'marketplace_id', type: 'integer', example: 1),
                    new OA\Property(property: 'company_id', type: 'integer', nullable: true, example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Store criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Store criado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/StoreResource'),
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
        path: '/api/v1/store/{store}',
        operationId: 'updateStore',
        summary: 'Atualiza uma loja existente',
        description: 'Todos os campos são opcionais (sometimes); envie apenas o que deseja alterar.',
        tags: ['Store'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'store', in: 'path', required: true, description: 'ID da loja', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Loja Principal'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'loja@sellerflow.com'),
                    new OA\Property(property: 'description', type: 'string', maxLength: 255, example: 'Loja de eletrônicos'),
                    new OA\Property(property: 'status_id', type: 'integer', example: 1),
                    new OA\Property(property: 'marketplace_id', type: 'integer', example: 1),
                    new OA\Property(property: 'company_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Store atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Store atualizado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/StoreResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Loja não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/v1/store/{store}',
        operationId: 'deleteStore',
        summary: 'Remove uma loja',
        tags: ['Store'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'store', in: 'path', required: true, description: 'ID da loja', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Store deletado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Store deletado com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Loja não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(): void
    {
    }
}
