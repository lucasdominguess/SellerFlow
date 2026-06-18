<?php

namespace App\Docs\Swagger\Finance;

use OpenApi\Attributes as OA;

// Documentação do CRUD de contas a pagar (App\Http\Controllers\Finance\AccountPayableController).
#[OA\Schema(
    schema: 'AccountPayableResource',
    title: 'Conta a pagar',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'company_id', type: 'integer', example: 1),
        new OA\Property(property: 'valor', type: 'number', format: 'float', example: 350.00),
        new OA\Property(property: 'vencimento', type: 'string', format: 'date', nullable: true, example: '2026-07-10'),
        new OA\Property(property: 'pago_em', type: 'string', format: 'date', nullable: true, example: '2026-07-08'),
        new OA\Property(property: 'status', type: 'string', enum: ['pendente', 'concluido', 'atrasado', 'cancelado'], example: 'pendente'),
        new OA\Property(property: 'categoria_financeira_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'forma_pagamento_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'origem_tipo', type: 'string', enum: ['compra', 'ajuste_manual', 'venda'], example: 'compra'),
        new OA\Property(property: 'origem_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'observacao', type: 'string', nullable: true, example: 'Pagamento ao fornecedor'),
    ],
    type: 'object'
)]
class AccountPayableDocs
{
    #[OA\Get(
        path: '/api/v1/account-payable',
        operationId: 'listAccountPayables',
        summary: 'Lista as contas a pagar de forma paginada',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'AccountPayables recuperados com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'AccountPayables recuperados com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/AccountPayableResource')),
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
        path: '/api/v1/account-payable/{account_payable}',
        operationId: 'showAccountPayable',
        summary: 'Exibe uma conta a pagar específica',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'account_payable', in: 'path', required: true, description: 'ID da conta a pagar', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'AccountPayable recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'AccountPayable recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/AccountPayableResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Conta a pagar não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/account-payable',
        operationId: 'createAccountPayable',
        summary: 'Cria uma nova conta a pagar',
        description: 'company_id é derivado do token autenticado.',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['valor'],
                properties: [
                    new OA\Property(property: 'valor', type: 'number', format: 'float', minimum: 0, example: 350.00),
                    new OA\Property(property: 'vencimento', type: 'string', format: 'date', nullable: true, example: '2026-07-10'),
                    new OA\Property(property: 'pago_em', type: 'string', format: 'date', nullable: true, example: '2026-07-08'),
                    new OA\Property(property: 'status', type: 'string', enum: ['pendente', 'concluido', 'atrasado', 'cancelado'], nullable: true, example: 'pendente'),
                    new OA\Property(property: 'categoria_financeira_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(property: 'forma_pagamento_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(property: 'origem_tipo', type: 'string', enum: ['compra', 'ajuste_manual', 'venda'], nullable: true, example: 'compra'),
                    new OA\Property(property: 'origem_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(property: 'observacao', type: 'string', maxLength: 1000, nullable: true, example: 'Pagamento ao fornecedor'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'AccountPayable criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'AccountPayable criado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/AccountPayableResource'),
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
        path: '/api/v1/account-payable/{account_payable}',
        operationId: 'updateAccountPayable',
        summary: 'Atualiza uma conta a pagar existente',
        description: 'Todos os campos são opcionais (sometimes); envie apenas o que deseja alterar.',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'account_payable', in: 'path', required: true, description: 'ID da conta a pagar', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'valor', type: 'number', format: 'float', minimum: 0, example: 350.00),
                    new OA\Property(property: 'vencimento', type: 'string', format: 'date', nullable: true, example: '2026-07-10'),
                    new OA\Property(property: 'pago_em', type: 'string', format: 'date', nullable: true, example: '2026-07-08'),
                    new OA\Property(property: 'status', type: 'string', enum: ['pendente', 'concluido', 'atrasado', 'cancelado'], example: 'concluido'),
                    new OA\Property(property: 'categoria_financeira_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(property: 'forma_pagamento_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(property: 'observacao', type: 'string', maxLength: 1000, nullable: true, example: 'Pagamento ao fornecedor'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'AccountPayable atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'AccountPayable atualizado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/AccountPayableResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Conta a pagar não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/v1/account-payable/{account_payable}',
        operationId: 'deleteAccountPayable',
        summary: 'Remove uma conta a pagar',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'account_payable', in: 'path', required: true, description: 'ID da conta a pagar', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'AccountPayable deletado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'AccountPayable deletado com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Conta a pagar não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(): void
    {
    }
}
