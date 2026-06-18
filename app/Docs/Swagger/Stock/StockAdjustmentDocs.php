<?php

namespace App\Docs\Swagger\Stock;

use OpenApi\Attributes as OA;

// Documentação dos ajustes de estoque (App\Http\Controllers\Adjustment\StockAdjustmentController).
// Rota registrada apenas com index, show e store.
#[OA\Schema(
    schema: 'StockAdjustmentResource',
    title: 'Ajuste de estoque',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'company_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'quantidade', type: 'integer', description: 'Positivo = entrada, negativo = saída', example: -3),
        new OA\Property(property: 'motivo', type: 'string', enum: ['perda', 'quebra', 'contagem_fisica', 'devolucao', 'outro'], example: 'perda'),
        new OA\Property(property: 'observacao', type: 'string', nullable: true, example: 'Produto avariado no transporte'),
    ],
    type: 'object'
)]
class StockAdjustmentDocs
{
    #[OA\Get(
        path: '/api/v1/stock-adjustment',
        operationId: 'listStockAdjustments',
        summary: 'Lista os ajustes de estoque de forma paginada',
        tags: ['Stock'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'StockAdjustments recuperados com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'StockAdjustments recuperados com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/StockAdjustmentResource')),
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
        path: '/api/v1/stock-adjustment/{stock_adjustment}',
        operationId: 'showStockAdjustment',
        summary: 'Exibe um ajuste de estoque específico',
        tags: ['Stock'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'stock_adjustment', in: 'path', required: true, description: 'ID do ajuste', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'StockAdjustment recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'StockAdjustment recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/StockAdjustmentResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Ajuste não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/stock-adjustment',
        operationId: 'createStockAdjustment',
        summary: 'Cria um ou mais ajustes de estoque',
        description: 'company_id e user_id são derivados do token. Cada item gera um ajuste e o respectivo movimento de estoque. A quantidade não pode ser zero (positivo = entrada, negativo = saída). Retorna o array dos ajustes criados.',
        tags: ['Stock'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['itens'],
                properties: [
                    new OA\Property(
                        property: 'itens',
                        type: 'array',
                        minItems: 1,
                        items: new OA\Items(
                            required: ['product_id', 'quantidade', 'motivo'],
                            properties: [
                                new OA\Property(property: 'product_id', type: 'integer', example: 1),
                                new OA\Property(property: 'quantidade', type: 'integer', description: 'Diferente de zero. Positivo = entrada, negativo = saída', example: -3),
                                new OA\Property(property: 'motivo', type: 'string', enum: ['perda', 'quebra', 'contagem_fisica', 'devolucao', 'outro'], example: 'perda'),
                                new OA\Property(property: 'observacao', type: 'string', nullable: true, example: 'Produto avariado no transporte'),
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
                description: 'StockAdjustment criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'StockAdjustment criado com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/StockAdjustmentResource')),
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
}
