<?php

namespace App\Docs\Swagger\Finance;

use OpenApi\Attributes as OA;

// Documentação do CRUD de contas a receber (App\Http\Controllers\Finance\AccountReceivableController).
#[OA\Schema(
    schema: 'AccountReceivableResource',
    title: 'Conta a receber',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'company_id', type: 'integer', example: 1),
        new OA\Property(property: 'store_id', type: 'integer', example: 1),
        new OA\Property(property: 'valor', type: 'number', format: 'float', example: 199.90),
        new OA\Property(property: 'previsao_recebimento', type: 'string', format: 'date', nullable: true, example: '2026-07-15'),
        new OA\Property(property: 'recebido_em', type: 'string', format: 'date', nullable: true, example: '2026-07-14'),
        new OA\Property(property: 'status', type: 'string', enum: ['pendente', 'concluido', 'atrasado', 'cancelado'], example: 'pendente'),
        new OA\Property(property: 'origem_tipo', type: 'string', enum: ['compra', 'ajuste_manual', 'venda'], example: 'venda'),
        new OA\Property(property: 'origem_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'observacao', type: 'string', nullable: true, example: 'Repasse do marketplace'),
    ],
    type: 'object'
)]
class AccountReceivableDocs
{
    #[OA\Get(
        path: '/api/v1/account-receivable',
        operationId: 'listAccountReceivables',
        summary: 'Lista as contas a receber de forma paginada',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'AccountReceivables recuperados com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'AccountReceivables recuperados com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/AccountReceivableResource')),
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
        path: '/api/v1/account-receivable/{account_receivable}',
        operationId: 'showAccountReceivable',
        summary: 'Exibe uma conta a receber específica',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'account_receivable', in: 'path', required: true, description: 'ID da conta a receber', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'AccountReceivable recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'AccountReceivable recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/AccountReceivableResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Conta a receber não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/account-receivable',
        operationId: 'createAccountReceivable',
        summary: 'Cria uma nova conta a receber',
        description: 'company_id e store_id são derivados do token autenticado.',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['valor'],
                properties: [
                    new OA\Property(property: 'valor', type: 'number', format: 'float', minimum: 0, example: 199.90),
                    new OA\Property(property: 'previsao_recebimento', type: 'string', format: 'date', nullable: true, example: '2026-07-15'),
                    new OA\Property(property: 'recebido_em', type: 'string', format: 'date', nullable: true, example: '2026-07-14'),
                    new OA\Property(property: 'status', type: 'string', enum: ['pendente', 'concluido', 'atrasado', 'cancelado'], nullable: true, example: 'pendente'),
                    new OA\Property(property: 'origem_tipo', type: 'string', enum: ['compra', 'ajuste_manual', 'venda'], nullable: true, example: 'venda'),
                    new OA\Property(property: 'origem_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(property: 'observacao', type: 'string', maxLength: 1000, nullable: true, example: 'Repasse do marketplace'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'AccountReceivable criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'AccountReceivable criado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/AccountReceivableResource'),
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
        path: '/api/v1/account-receivable/{account_receivable}',
        operationId: 'updateAccountReceivable',
        summary: 'Atualiza uma conta a receber existente',
        description: 'Todos os campos são opcionais (sometimes); envie apenas o que deseja alterar.',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'account_receivable', in: 'path', required: true, description: 'ID da conta a receber', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'valor', type: 'number', format: 'float', minimum: 0, example: 199.90),
                    new OA\Property(property: 'previsao_recebimento', type: 'string', format: 'date', nullable: true, example: '2026-07-15'),
                    new OA\Property(property: 'recebido_em', type: 'string', format: 'date', nullable: true, example: '2026-07-14'),
                    new OA\Property(property: 'status', type: 'string', enum: ['pendente', 'concluido', 'atrasado', 'cancelado'], example: 'concluido'),
                    new OA\Property(property: 'observacao', type: 'string', maxLength: 1000, nullable: true, example: 'Repasse do marketplace'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'AccountReceivable atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'AccountReceivable atualizado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/AccountReceivableResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Conta a receber não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/v1/account-receivable/{account_receivable}',
        operationId: 'deleteAccountReceivable',
        summary: 'Remove uma conta a receber',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'account_receivable', in: 'path', required: true, description: 'ID da conta a receber', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'AccountReceivable deletado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'AccountReceivable deletado com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Conta a receber não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(): void
    {
    }
}
