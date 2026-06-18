<?php

namespace App\Docs\Swagger\Sales;

use OpenApi\Attributes as OA;

// Documentação do CRUD de vendas (App\Http\Controllers\Sales\SaleController).
#[OA\Schema(
    schema: 'SaleItemResource',
    title: 'Item da venda',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'venda_id', type: 'integer', example: 1),
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'quantidade', type: 'integer', example: 2),
        new OA\Property(property: 'valor_unitario', type: 'number', format: 'float', example: 49.90),
        new OA\Property(property: 'valor_total', type: 'number', format: 'float', example: 99.80),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'SaleResource',
    title: 'Venda',
    description: 'O array itens vem preenchido em show/store/update; no index é retornado vazio.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'company_id', type: 'integer', example: 1),
        new OA\Property(property: 'store_id', type: 'integer', example: 1),
        new OA\Property(property: 'market_place_id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'status', type: 'string', enum: ['pendente', 'concluido', 'atrasado', 'cancelado'], nullable: true, example: 'pendente'),
        new OA\Property(property: 'numero_pedido', type: 'string', example: 'PED-2026-001'),
        new OA\Property(property: 'data_venda', type: 'string', format: 'date', nullable: true, example: '2026-06-18'),
        new OA\Property(property: 'valor_bruto', type: 'number', format: 'float', example: 99.80),
        new OA\Property(property: 'taxa_marketplace', type: 'number', format: 'float', example: 12.00),
        new OA\Property(property: 'valor_frete', type: 'number', format: 'float', example: 0.00),
        new OA\Property(property: 'valor_liquido', type: 'number', format: 'float', description: 'Calculado pelo Service', example: 87.80),
        new OA\Property(property: 'data_previsao_repasse', type: 'string', format: 'date', nullable: true, example: '2026-07-01'),
        new OA\Property(property: 'observacao', type: 'string', nullable: true, example: 'Pedido com frete grátis'),
        new OA\Property(property: 'itens', type: 'array', items: new OA\Items(ref: '#/components/schemas/SaleItemResource')),
    ],
    type: 'object'
)]
class SaleDocs
{
    #[OA\Get(
        path: '/api/v1/sales',
        operationId: 'listSales',
        summary: 'Lista as vendas de forma paginada',
        tags: ['Sales'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Vendas recuperadas com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Vendas recuperadas com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/SaleResource')),
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
        path: '/api/v1/sales/{sale}',
        operationId: 'showSale',
        summary: 'Exibe uma venda específica (com itens)',
        tags: ['Sales'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'sale', in: 'path', required: true, description: 'ID da venda', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Venda recuperada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Venda recuperada com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/SaleResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Venda não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/sales',
        operationId: 'createSale',
        summary: 'Cria uma nova venda com seus itens',
        description: 'company_id, store_id e user_id são derivados do token autenticado. numero_pedido é único por marketplace. valor_liquido é calculado pelo Service.',
        tags: ['Sales'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['market_place_id', 'numero_pedido', 'data_venda', 'valor_bruto', 'venda_itens'],
                properties: [
                    new OA\Property(property: 'market_place_id', type: 'integer', example: 1),
                    new OA\Property(property: 'numero_pedido', type: 'string', maxLength: 255, example: 'PED-2026-001'),
                    new OA\Property(property: 'data_venda', type: 'string', format: 'date', example: '2026-06-18'),
                    new OA\Property(property: 'valor_bruto', type: 'number', format: 'float', minimum: 0, example: 99.80),
                    new OA\Property(property: 'taxa_marketplace', type: 'number', format: 'float', minimum: 0, nullable: true, example: 12.00),
                    new OA\Property(property: 'valor_frete', type: 'number', format: 'float', minimum: 0, nullable: true, example: 0.00),
                    new OA\Property(property: 'data_previsao_repasse', type: 'string', format: 'date', nullable: true, example: '2026-07-01'),
                    new OA\Property(property: 'observacao', type: 'string', nullable: true, example: 'Pedido com frete grátis'),
                    new OA\Property(
                        property: 'venda_itens',
                        type: 'array',
                        minItems: 1,
                        items: new OA\Items(
                            required: ['product_id', 'quantidade', 'valor_unitario'],
                            properties: [
                                new OA\Property(property: 'product_id', type: 'integer', example: 1),
                                new OA\Property(property: 'quantidade', type: 'integer', minimum: 1, example: 2),
                                new OA\Property(property: 'valor_unitario', type: 'number', format: 'float', minimum: 0, example: 49.90),
                            ],
                            type: 'object'
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Venda criada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Venda criada com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/SaleResource'),
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
        path: '/api/v1/sales/{sale}',
        operationId: 'updateSale',
        summary: 'Atualiza uma venda existente',
        description: 'Identidade (company_id, store_id, user_id) é imutável. O status "atrasado" é exclusivo do financeiro. Campos opcionais (sometimes). Itens não são alterados por este endpoint.',
        tags: ['Sales'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'sale', in: 'path', required: true, description: 'ID da venda', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'market_place_id', type: 'integer', example: 1),
                    new OA\Property(property: 'numero_pedido', type: 'string', maxLength: 255, example: 'PED-2026-001'),
                    new OA\Property(property: 'data_venda', type: 'string', format: 'date', example: '2026-06-18'),
                    new OA\Property(property: 'valor_bruto', type: 'number', format: 'float', minimum: 0, example: 99.80),
                    new OA\Property(property: 'taxa_marketplace', type: 'number', format: 'float', minimum: 0, nullable: true, example: 12.00),
                    new OA\Property(property: 'valor_frete', type: 'number', format: 'float', minimum: 0, nullable: true, example: 0.00),
                    new OA\Property(property: 'data_previsao_repasse', type: 'string', format: 'date', nullable: true, example: '2026-07-01'),
                    new OA\Property(property: 'status', type: 'string', enum: ['pendente', 'concluido', 'cancelado'], example: 'concluido'),
                    new OA\Property(property: 'observacao', type: 'string', nullable: true, example: 'Pedido com frete grátis'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Venda atualizada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Venda atualizada com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/SaleResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Venda não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/v1/sales/{sale}',
        operationId: 'deleteSale',
        summary: 'Remove uma venda',
        tags: ['Sales'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'sale', in: 'path', required: true, description: 'ID da venda', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Venda deletada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Venda deletada com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Venda não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(): void
    {
    }
}
