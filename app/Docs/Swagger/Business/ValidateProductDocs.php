<?php

namespace App\Docs\Swagger\Business;

use OpenApi\Attributes as OA;

// Documentação de validação de produtos (App\Http\Controllers\Business\ValidateProductController).
// O ResponseDTO expõe apenas o snapshot financeiro calculado pelo Service.
#[OA\Schema(
    schema: 'ValidateProductResource',
    title: 'Resultado de validação de produto',
    description: 'Indicadores financeiros calculados pelo Service (nunca recebidos do cliente).',
    properties: [
        new OA\Property(property: 'price_sale', type: 'number', format: 'float', example: 49.90),
        new OA\Property(property: 'price_buy', type: 'number', format: 'float', example: 20.00),
        new OA\Property(property: 'cust_additional', type: 'number', format: 'float', example: 2.50),
        new OA\Property(property: 'fee_percent', type: 'number', format: 'float', description: 'Taxa percentual do marketplace', example: 12.0),
        new OA\Property(property: 'fee_fixed', type: 'number', format: 'float', description: 'Taxa fixa do marketplace', example: 4.0),
        new OA\Property(property: 'fee_total', type: 'number', format: 'float', description: 'Taxa total (percentual + fixa)', example: 9.99),
        new OA\Property(property: 'profit_amount', type: 'number', format: 'float', description: 'Lucro em reais', example: 17.41),
        new OA\Property(property: 'profit_margin', type: 'number', format: 'float', description: 'Margem de lucro (%)', example: 34.89),
        new OA\Property(property: 'breakeven_roas', type: 'number', format: 'float', description: 'ROAS de equilíbrio', example: 2.87),
    ],
    type: 'object'
)]
class ValidateProductDocs
{
    #[OA\Get(
        path: '/api/v1/validate-product',
        operationId: 'listValidateProducts',
        summary: 'Lista os produtos validados de forma paginada',
        tags: ['Validate Product'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'ValidateProducts recuperados com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'ValidateProducts recuperados com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ValidateProductResource')),
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
        path: '/api/v1/validate-product/{validate_product}',
        operationId: 'showValidateProduct',
        summary: 'Exibe um produto validado específico',
        tags: ['Validate Product'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'validate_product', in: 'path', required: true, description: 'ID do produto validado', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'ValidateProduct recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'ValidateProduct recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ValidateProductResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Produto validado não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/validate-product',
        operationId: 'createValidateProduct',
        summary: 'Cria (persiste) um produto validado',
        description: 'company_id e user_id são derivados do token autenticado e não devem ser enviados. Os indicadores financeiros são calculados pelo Service.',
        tags: ['Validate Product'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'price_sale', 'price_buy', 'marketplace_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Camiseta Azul'),
                    new OA\Property(property: 'brand', type: 'string', maxLength: 255, nullable: true, example: 'Marca X'),
                    new OA\Property(property: 'description', type: 'string', maxLength: 1000, nullable: true, example: 'Camiseta 100% algodão'),
                    new OA\Property(property: 'catalog_link', type: 'string', format: 'uri', maxLength: 2048, nullable: true, example: 'https://fornecedor.com/produto'),
                    new OA\Property(property: 'supplier_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(property: 'price_sale', type: 'number', format: 'float', minimum: 0, example: 49.90),
                    new OA\Property(property: 'price_buy', type: 'number', format: 'float', minimum: 0, example: 20.00),
                    new OA\Property(property: 'cust_additional', type: 'number', format: 'float', minimum: 0, example: 2.50),
                    new OA\Property(property: 'marketplace_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'ValidateProduct criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'ValidateProduct criado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ValidateProductResource'),
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
        path: '/api/v1/validate-product/{validate_product}',
        operationId: 'updateValidateProduct',
        summary: 'Atualiza um produto validado',
        description: 'O FormRequest de atualização não define campos validáveis no momento; o corpo é processado pelo Service.',
        tags: ['Validate Product'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'validate_product', in: 'path', required: true, description: 'ID do produto validado', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(type: 'object')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'ValidateProduct atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'ValidateProduct atualizado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ValidateProductResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Produto validado não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/v1/validate-product/{validate_product}',
        operationId: 'deleteValidateProduct',
        summary: 'Remove um produto validado',
        tags: ['Validate Product'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'validate_product', in: 'path', required: true, description: 'ID do produto validado', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'ValidateProduct deletado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'ValidateProduct deletado com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Produto validado não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/check-validate-product',
        operationId: 'checkValidateProduct',
        summary: 'Calcula os indicadores financeiros sem persistir (preview)',
        description: 'Retorna o cálculo de taxas, lucro e ROAS de equilíbrio com base no marketplace informado, sem salvar o produto.',
        tags: ['Validate Product'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['price_sale', 'price_buy', 'marketplace_id'],
                properties: [
                    new OA\Property(property: 'price_sale', type: 'number', format: 'float', minimum: 0, example: 49.90),
                    new OA\Property(property: 'price_buy', type: 'number', format: 'float', minimum: 0, example: 20.00),
                    new OA\Property(property: 'cust_additional', type: 'number', format: 'float', minimum: 0, example: 2.50),
                    new OA\Property(property: 'marketplace_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'ValidateProduct validado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'ValidateProduct validado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ValidateProductResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function validate(): void
    {
    }
}
