<?php

namespace App\Docs\Swagger\Finance;

use OpenApi\Attributes as OA;

// Documentação do resumo agregado do seller (App\Http\Controllers\Finance\DashboardController).
#[OA\Schema(
    schema: 'DashboardTopProductResource',
    title: 'Produto no top de vendas',
    properties: [
        new OA\Property(property: 'product_id', type: 'integer', example: 10),
        new OA\Property(property: 'sku', type: 'string', example: 'SKU-001'),
        new OA\Property(property: 'name', type: 'string', example: 'Capa de Celular'),
        new OA\Property(property: 'quantidade', type: 'integer', description: 'Total vendido no período', example: 42),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'DashboardFinanceBlockResource',
    title: 'Bloco financeiro (a pagar ou a receber)',
    description: 'Para a_receber o campo liquidado é "recebido_periodo"; para a_pagar é "pago_periodo".',
    properties: [
        new OA\Property(property: 'pendente', type: 'number', format: 'float', description: 'Total em aberto (status pendente)', example: 320.00),
        new OA\Property(property: 'recebido_periodo', type: 'number', format: 'float', description: 'Liquidado no período (apenas a_receber)', example: 1500.00),
        new OA\Property(property: 'pago_periodo', type: 'number', format: 'float', description: 'Liquidado no período (apenas a_pagar)', example: 900.00),
        new OA\Property(property: 'atrasado', type: 'number', format: 'float', description: 'Em aberto e já vencido (calculado pela data de vencimento)', example: 50.00),
        new OA\Property(property: 'a_vencer_7d', type: 'number', format: 'float', description: 'Em aberto a vencer nos próximos 7 dias', example: 120.00),
        new OA\Property(property: 'a_vencer_30d', type: 'number', format: 'float', description: 'Em aberto a vencer nos próximos 30 dias (cumulativo, inclui os 7d)', example: 300.00),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'DashboardResource',
    title: 'Resumo do dashboard',
    properties: [
        new OA\Property(
            property: 'periodo',
            type: 'object',
            properties: [
                new OA\Property(property: 'inicio', type: 'string', format: 'date', example: '2026-06-01'),
                new OA\Property(property: 'fim', type: 'string', format: 'date', example: '2026-06-30'),
            ]
        ),
        new OA\Property(
            property: 'vendas',
            type: 'object',
            description: 'Vendas do período, excluindo canceladas',
            properties: [
                new OA\Property(property: 'pedidos', type: 'integer', example: 12),
                new OA\Property(property: 'total_bruto', type: 'number', format: 'float', example: 4500.00),
                new OA\Property(property: 'total_liquido', type: 'number', format: 'float', example: 3800.00),
            ]
        ),
        new OA\Property(
            property: 'compras',
            type: 'object',
            description: 'Compras do período, excluindo canceladas',
            properties: [
                new OA\Property(property: 'compras', type: 'integer', example: 4),
                new OA\Property(property: 'total', type: 'number', format: 'float', example: 2100.00),
            ]
        ),
        new OA\Property(
            property: 'financeiro',
            type: 'object',
            properties: [
                new OA\Property(property: 'a_receber', ref: '#/components/schemas/DashboardFinanceBlockResource'),
                new OA\Property(property: 'a_pagar', ref: '#/components/schemas/DashboardFinanceBlockResource'),
            ]
        ),
        new OA\Property(
            property: 'estoque',
            type: 'object',
            properties: [
                new OA\Property(property: 'skus_com_saldo', type: 'integer', example: 35),
                new OA\Property(property: 'skus_zerados', type: 'integer', example: 8),
                new OA\Property(property: 'total_investido', type: 'number', format: 'float', description: 'Valor parado em estoque, calculado por FIFO', example: 7340.50),
            ]
        ),
        new OA\Property(property: 'top_produtos', type: 'array', items: new OA\Items(ref: '#/components/schemas/DashboardTopProductResource')),
    ],
    type: 'object'
)]
class DashboardDocs
{
    #[OA\Get(
        path: '/api/v1/finance/dashboard',
        operationId: 'getDashboard',
        summary: 'Retorna o resumo agregado do seller no período',
        description: 'Agrega vendas, compras, contas a pagar/receber, estoque e top produtos. Sem datas, assume o mês corrente.',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'start_date', in: 'query', required: false, description: 'Data inicial (default: início do mês corrente)', schema: new OA\Schema(type: 'string', format: 'date', example: '2026-06-01')),
            new OA\Parameter(name: 'end_date', in: 'query', required: false, description: 'Data final (>= start_date; default: fim do mês corrente)', schema: new OA\Schema(type: 'string', format: 'date', example: '2026-06-30')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Dashboard recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Dashboard recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/DashboardResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function index(): void
    {
    }
}
