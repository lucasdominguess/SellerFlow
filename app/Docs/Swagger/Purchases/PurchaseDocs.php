<?php

namespace App\Docs\Swagger\Purchases;

use OpenApi\Attributes as OA;

// Documentação do CRUD de compras (App\Http\Controllers\Purchases\PurchaseController).
#[OA\Schema(
    schema: 'PurchaseItemResource',
    title: 'Item da compra',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'compra_id', type: 'integer', example: 1),
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'quantidade', type: 'integer', example: 10),
        new OA\Property(property: 'valor_unitario', type: 'number', format: 'float', example: 20.00),
        new OA\Property(property: 'valor_total', type: 'number', format: 'float', example: 200.00),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'PurchaseResource',
    title: 'Compra',
    description: 'O array itens vem preenchido em show/store/update; no index é retornado vazio (não é carregado).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'company_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'store_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'fornecedor_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'user_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'forma_pagamento_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'status', type: 'string', enum: ['pendente', 'concluido', 'atrasado', 'cancelado'], nullable: true, example: 'pendente'),
        new OA\Property(property: 'numero_nota', type: 'string', nullable: true, example: 'NF-12345'),
        new OA\Property(property: 'data_compra', type: 'string', format: 'date', nullable: true, example: '2026-06-18'),
        new OA\Property(property: 'valor_total', type: 'number', format: 'float', nullable: true, example: 200.00),
        new OA\Property(property: 'numero_parcelas', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'observacao', type: 'string', nullable: true, example: 'Compra mensal'),
        new OA\Property(property: 'itens', type: 'array', items: new OA\Items(ref: '#/components/schemas/PurchaseItemResource')),
    ],
    type: 'object'
)]
class PurchaseDocs
{
    #[OA\Get(
        path: '/api/v1/purchases',
        operationId: 'listPurchases',
        summary: 'Lista as compras de forma paginada',
        tags: ['Purchases'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Compras recuperadas com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Compras recuperados com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/PurchaseResource')),
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
        path: '/api/v1/purchases/{purchase}',
        operationId: 'showPurchase',
        summary: 'Exibe uma compra específica (com itens)',
        tags: ['Purchases'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'purchase', in: 'path', required: true, description: 'ID da compra', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Compra recuperada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Compra recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/PurchaseResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Compra não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/purchases',
        operationId: 'createPurchase',
        summary: 'Cria uma nova compra com seus itens',
        description: 'company_id, store_id e user_id são derivados do token autenticado. O status nasce sempre como "pendente". O valor_total é calculado pelo Service a partir dos itens.',
        tags: ['Purchases'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fornecedor_id', 'forma_pagamento_id', 'data_compra', 'itens'],
                properties: [
                    new OA\Property(property: 'fornecedor_id', type: 'integer', example: 1),
                    new OA\Property(property: 'forma_pagamento_id', type: 'integer', example: 1),
                    new OA\Property(property: 'numero_nota', type: 'string', maxLength: 255, nullable: true, example: 'NF-12345'),
                    new OA\Property(property: 'data_compra', type: 'string', format: 'date', example: '2026-06-18'),
                    new OA\Property(property: 'numero_parcelas', type: 'integer', minimum: 1, nullable: true, example: 1),
                    new OA\Property(property: 'observacao', type: 'string', maxLength: 1000, nullable: true, example: 'Compra mensal'),
                    new OA\Property(
                        property: 'itens',
                        type: 'array',
                        minItems: 1,
                        items: new OA\Items(
                            required: ['product_id', 'quantidade', 'valor_unitario'],
                            properties: [
                                new OA\Property(property: 'product_id', type: 'integer', example: 1),
                                new OA\Property(property: 'quantidade', type: 'integer', minimum: 1, example: 10),
                                new OA\Property(property: 'valor_unitario', type: 'number', format: 'float', minimum: 0, example: 20.00),
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
                description: 'Compra criada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Compra criado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/PurchaseResource'),
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
        path: '/api/v1/purchases/{purchase}',
        operationId: 'updatePurchase',
        summary: 'Atualiza uma compra existente',
        description: 'Identidade (company_id, store_id, user_id) é imutável. O status "atrasado" é exclusivo do financeiro e não pode ser definido aqui. Campos opcionais (sometimes).',
        tags: ['Purchases'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'purchase', in: 'path', required: true, description: 'ID da compra', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'fornecedor_id', type: 'integer', example: 1),
                    new OA\Property(property: 'forma_pagamento_id', type: 'integer', example: 1),
                    new OA\Property(property: 'status', type: 'string', enum: ['pendente', 'concluido', 'cancelado'], example: 'concluido'),
                    new OA\Property(property: 'numero_nota', type: 'string', maxLength: 255, nullable: true, example: 'NF-12345'),
                    new OA\Property(property: 'data_compra', type: 'string', format: 'date', example: '2026-06-18'),
                    new OA\Property(property: 'numero_parcelas', type: 'integer', minimum: 1, example: 1),
                    new OA\Property(property: 'observacao', type: 'string', maxLength: 1000, nullable: true, example: 'Compra mensal'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Compra atualizada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Compra atualizado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/PurchaseResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Compra não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/v1/purchases/{purchase}',
        operationId: 'deletePurchase',
        summary: 'Remove uma compra',
        tags: ['Purchases'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'purchase', in: 'path', required: true, description: 'ID da compra', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Compra deletada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Compra deletado com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Compra não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(): void
    {
    }
}
