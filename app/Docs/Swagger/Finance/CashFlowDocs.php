<?php

namespace App\Docs\Swagger\Finance;

use OpenApi\Attributes as OA;

// Documentação do relatório de fluxo de caixa realizado (App\Http\Controllers\Finance\CashFlowController).
#[OA\Schema(
    schema: 'CashFlowEntryResource',
    title: 'Período do fluxo de caixa',
    properties: [
        new OA\Property(property: 'period', type: 'string', format: 'date', example: '2026-06-01'),
        new OA\Property(property: 'entradas', type: 'number', format: 'float', example: 1500.00),
        new OA\Property(property: 'saidas', type: 'number', format: 'float', example: 900.00),
        new OA\Property(property: 'saldo', type: 'number', format: 'float', description: 'entradas - saidas do período', example: 600.00),
        new OA\Property(property: 'saldo_acumulado', type: 'number', format: 'float', description: 'Saldo corrente até este período', example: 600.00),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'CashFlowReportResource',
    title: 'Relatório de fluxo de caixa',
    properties: [
        new OA\Property(property: 'granularity', type: 'string', enum: ['day', 'week', 'month'], example: 'month'),
        new OA\Property(property: 'start_date', type: 'string', format: 'date', example: '2026-06-01'),
        new OA\Property(property: 'end_date', type: 'string', format: 'date', example: '2026-06-30'),
        new OA\Property(
            property: 'summary',
            type: 'object',
            properties: [
                new OA\Property(property: 'total_entradas', type: 'number', format: 'float', example: 1500.00),
                new OA\Property(property: 'total_saidas', type: 'number', format: 'float', example: 900.00),
                new OA\Property(property: 'saldo', type: 'number', format: 'float', example: 600.00),
            ]
        ),
        new OA\Property(property: 'periods', type: 'array', items: new OA\Items(ref: '#/components/schemas/CashFlowEntryResource')),
    ],
    type: 'object'
)]
class CashFlowDocs
{
    #[OA\Get(
        path: '/api/v1/finance/cash-flow',
        operationId: 'getCashFlow',
        summary: 'Retorna o fluxo de caixa realizado no período',
        description: 'Agrega entradas e saídas realizadas entre start_date e end_date, com saldo acumulado por período.',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'start_date', in: 'query', required: true, description: 'Data inicial', schema: new OA\Schema(type: 'string', format: 'date', example: '2026-06-01')),
            new OA\Parameter(name: 'end_date', in: 'query', required: true, description: 'Data final (>= start_date)', schema: new OA\Schema(type: 'string', format: 'date', example: '2026-06-30')),
            new OA\Parameter(name: 'granularity', in: 'query', required: false, description: 'Agrupamento dos períodos', schema: new OA\Schema(type: 'string', enum: ['day', 'week', 'month'], example: 'month')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Fluxo de caixa recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Fluxo de caixa recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CashFlowReportResource'),
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
