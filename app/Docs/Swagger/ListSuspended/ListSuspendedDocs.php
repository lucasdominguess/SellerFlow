<?php

namespace App\Docs\Swagger\ListSuspended;

use OpenApi\Attributes as OA;

// Documentação do endpoint de listagens auxiliares (App\Http\Controllers\ListSuspended\ListSuspendedController).
class ListSuspendedDocs
{
    #[OA\Get(
        path: '/api/v1/list',
        operationId: 'listSuspendedIndex',
        summary: 'Lista itens auxiliares para selects conforme o parâmetro informado',
        description: 'Retorna a lista do recurso indicado em "params". Os campos de cada item variam conforme o recurso (categoria-financeira, fornecedor, forma-pagamento, marketplace, produto, company).',
        tags: ['List Suspended'],
        parameters: [
            new OA\Parameter(
                name: 'params',
                in: 'query',
                required: true,
                description: 'Recurso a ser listado',
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['categoria-financeira', 'fornecedor', 'forma-pagamento', 'marketplace', 'produto', 'company'],
                    example: 'produto'
                )
            ),
            new OA\Parameter(
                name: 'status_id',
                in: 'query',
                required: false,
                description: 'Filtra pelos itens com o status informado',
                schema: new OA\Schema(type: 'integer', nullable: true, example: 1)
            ),
            new OA\Parameter(
                name: 'name',
                in: 'query',
                required: false,
                description: 'Filtra pelo nome do item',
                schema: new OA\Schema(type: 'string', nullable: true, example: 'Camiseta')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de itens retornada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Lista de itens retornada com sucesso'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Camiseta Básica'),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Parâmetro inválido', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function list(): void
    {
    }
}
