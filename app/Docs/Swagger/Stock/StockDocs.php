<?php

namespace App\Docs\Swagger\Stock;

use OpenApi\Attributes as OA;

// Documentação de estoque (App\Http\Controllers\Stock\StockController):
// CRUD de movimentos + consultas de quantidade e investimento.
#[OA\Schema(
    schema: 'StockBalanceResource',
    title: 'Saldo de estoque do produto',
    properties: [
        new OA\Property(property: 'company_id', type: 'integer', example: 1),
        new OA\Property(property: 'company_name', type: 'string', example: 'Loja do João LTDA'),
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'sku', type: 'string', example: 'CAM-AZUL-001'),
        new OA\Property(property: 'product_name', type: 'string', example: 'Camiseta Azul'),
        new OA\Property(property: 'last_adjustment_user', type: 'string', nullable: true, example: 'João Seller'),
        new OA\Property(property: 'total_entradas', type: 'integer', example: 100),
        new OA\Property(property: 'total_saidas', type: 'integer', example: 40),
        new OA\Property(property: 'total_ajustes_positivos', type: 'integer', example: 5),
        new OA\Property(property: 'total_ajustes_negativos', type: 'integer', example: 3),
        new OA\Property(property: 'saldo_atual', type: 'integer', example: 62),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'StockInvestmentResource',
    title: 'Investimento em estoque do produto',
    properties: [
        new OA\Property(property: 'company_id', type: 'integer', example: 1),
        new OA\Property(property: 'company_name', type: 'string', example: 'Loja do João LTDA'),
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'sku', type: 'string', example: 'CAM-AZUL-001'),
        new OA\Property(property: 'product_name', type: 'string', example: 'Camiseta Azul'),
        new OA\Property(property: 'saldo_atual', type: 'integer', example: 62),
        new OA\Property(property: 'valor_investido', type: 'number', format: 'float', example: 1240.00),
        new OA\Property(
            property: 'composicao',
            type: 'array',
            description: 'Camadas FIFO que compõem o saldo atual',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'qty', type: 'integer', example: 40),
                    new OA\Property(property: 'preco', type: 'number', format: 'float', example: 20.00),
                ],
                type: 'object'
            )
        ),
        new OA\Property(property: 'tem_unidade_sem_custo', type: 'boolean', description: 'true se há unidades sem camada de compra (ex.: ajuste positivo)', example: false),
    ],
    type: 'object'
)]
class StockDocs
{
    #[OA\Get(
        path: '/api/v1/stock-check-quantity',
        operationId: 'checkStockQuantity',
        summary: 'Consulta a quantidade de produtos em estoque (paginado)',
        description: 'company_id é derivado do token autenticado. Filtros opcionais por produto, nome ou SKU.',
        tags: ['Stock'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'product_id', in: 'query', required: false, description: 'Filtra por produto', schema: new OA\Schema(type: 'integer', example: 1)),
            new OA\Parameter(name: 'product_name', in: 'query', required: false, description: 'Filtra por nome do produto', schema: new OA\Schema(type: 'string', example: 'Camiseta')),
            new OA\Parameter(name: 'sku', in: 'query', required: false, description: 'Filtra por SKU', schema: new OA\Schema(type: 'string', example: 'CAM-AZUL-001')),
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Quantidade de produtos em estoque recuperada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Quantidade de produtos em estoque recuperada com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/StockBalanceResource')),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function checkQuantityProductsInStock(): void
    {
    }

    #[OA\Get(
        path: '/api/v1/stock-investment',
        operationId: 'stockInvestment',
        summary: 'Consulta o valor investido em estoque (paginado)',
        description: 'Calcula o valor parado no estoque por produto via FIFO. company_id vem do token. Retorna total_investido agregado no nível raiz, além da lista paginada.',
        tags: ['Stock'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'product_id', in: 'query', required: false, description: 'Filtra por produto', schema: new OA\Schema(type: 'integer', example: 1)),
            new OA\Parameter(name: 'product_name', in: 'query', required: false, description: 'Filtra por nome do produto', schema: new OA\Schema(type: 'string', example: 'Camiseta')),
            new OA\Parameter(name: 'sku', in: 'query', required: false, description: 'Filtra por SKU', schema: new OA\Schema(type: 'string', example: 'CAM-AZUL-001')),
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Valor investido no estoque recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Valor investido no estoque recuperado com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/StockInvestmentResource')),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
                        new OA\Property(property: 'total_investido', type: 'number', format: 'float', description: 'Soma do valor investido considerando os filtros aplicados', example: 8540.50),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function investmentInStock(): void
    {
    }

    #[OA\Get(
        path: '/api/v1/stock',
        operationId: 'listStock',
        summary: 'Lista os movimentos de estoque de forma paginada',
        description: 'Atenção: o StockResponseDTO atual não expõe campos; cada item é retornado como objeto vazio até o contrato ser definido.',
        tags: ['Stock'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Stocks recuperados com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Stocks recuperados com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object')),
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
        path: '/api/v1/stock/{stock}',
        operationId: 'showStock',
        summary: 'Exibe um movimento de estoque específico',
        description: 'Atenção: o StockResponseDTO atual não expõe campos; data é retornado como objeto vazio.',
        tags: ['Stock'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'stock', in: 'path', required: true, description: 'ID do movimento de estoque', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Stock recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Stock recuperado com sucesso'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Movimento não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/stock',
        operationId: 'createStock',
        summary: 'Registra um movimento de estoque',
        description: 'user_id e company_id são derivados do usuário autenticado. tipo e origem_tipo são enums. O StockResponseDTO atual retorna data vazio.',
        tags: ['Stock'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id', 'quantidade', 'tipo', 'origem_tipo', 'origem_id'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                    new OA\Property(property: 'quantidade', type: 'integer', minimum: 1, example: 10),
                    new OA\Property(property: 'tipo', type: 'string', enum: ['entrada', 'saida', 'ajuste'], example: 'entrada'),
                    new OA\Property(property: 'origem_tipo', type: 'string', enum: ['compra', 'venda', 'ajuste_manual'], example: 'compra'),
                    new OA\Property(property: 'origem_id', type: 'integer', description: 'ID do documento de origem (compra, venda ou ajuste)', example: 1),
                    new OA\Property(property: 'observacao', type: 'string', maxLength: 255, nullable: true, example: 'Entrada por compra #1'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Stock criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Stock criado com sucesso'),
                        new OA\Property(property: 'data', type: 'object'),
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
        path: '/api/v1/stock/{stock}',
        operationId: 'updateStock',
        summary: 'Atualiza um movimento de estoque',
        description: 'O StockUpdateRequest atual não define campos validáveis; o corpo é processado pelo Service. O StockResponseDTO retorna data vazio.',
        tags: ['Stock'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'stock', in: 'path', required: true, description: 'ID do movimento de estoque', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(type: 'object')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Stock atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Stock atualizado com sucesso'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Movimento não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/v1/stock/{stock}',
        operationId: 'deleteStock',
        summary: 'Remove um movimento de estoque',
        tags: ['Stock'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'stock', in: 'path', required: true, description: 'ID do movimento de estoque', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Stock deletado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Stock deletado com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Movimento não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(): void
    {
    }
}
