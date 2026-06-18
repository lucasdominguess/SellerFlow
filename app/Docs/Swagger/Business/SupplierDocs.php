<?php

namespace App\Docs\Swagger\Business;

use OpenApi\Attributes as OA;

// Documentação do CRUD de fornecedores (App\Http\Controllers\Business\SupplierController).
#[OA\Schema(
    schema: 'SupplierResource',
    title: 'Fornecedor',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Distribuidora ABC'),
        new OA\Property(property: 'responsavel', type: 'string', nullable: true, example: 'Maria Souza'),
        new OA\Property(property: 'cnpj', type: 'string', example: '12.345.678/0001-90'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'contato@abc.com'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '(11) 99999-9999'),
        new OA\Property(property: 'address', type: 'string', nullable: true, example: 'Rua das Flores, 123'),
        new OA\Property(property: 'link_catalog', type: 'string', nullable: true, example: 'https://abc.com/catalogo'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Fornecedor de eletrônicos'),
        new OA\Property(property: 'status_id', type: 'integer', example: 1),
    ],
    type: 'object'
)]
class SupplierDocs
{
    #[OA\Get(
        path: '/api/v1/supplier',
        operationId: 'listSuppliers',
        summary: 'Lista os fornecedores de forma paginada',
        tags: ['Supplier'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
            new OA\Parameter(name: 'name', in: 'query', required: false, description: 'Filtro opcional por nome', schema: new OA\Schema(type: 'string', example: 'Distribuidora')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Fornecedores recuperados com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Fornecedores recuperados com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/SupplierResource')),
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
        path: '/api/v1/supplier/{supplier}',
        operationId: 'showSupplier',
        summary: 'Exibe um fornecedor específico',
        tags: ['Supplier'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'supplier', in: 'path', required: true, description: 'ID do fornecedor', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Fornecedor recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Fornecedor recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/SupplierResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Fornecedor não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/supplier',
        operationId: 'createSupplier',
        summary: 'Cria um novo fornecedor',
        tags: ['Supplier'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'cnpj', 'email', 'status_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Distribuidora ABC'),
                    new OA\Property(property: 'responsavel', type: 'string', maxLength: 255, nullable: true, example: 'Maria Souza'),
                    new OA\Property(property: 'cnpj', type: 'string', maxLength: 18, example: '12.345.678/0001-90'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'contato@abc.com'),
                    new OA\Property(property: 'phone', type: 'string', maxLength: 20, nullable: true, example: '(11) 99999-9999'),
                    new OA\Property(property: 'address', type: 'string', maxLength: 255, nullable: true, example: 'Rua das Flores, 123'),
                    new OA\Property(property: 'link_catalog', type: 'string', format: 'uri', maxLength: 255, nullable: true, example: 'https://abc.com/catalogo'),
                    new OA\Property(property: 'description', type: 'string', maxLength: 255, nullable: true, example: 'Fornecedor de eletrônicos'),
                    new OA\Property(property: 'status_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Fornecedor criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Fornecedor criado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/SupplierResource'),
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
        path: '/api/v1/supplier/{supplier}',
        operationId: 'updateSupplier',
        summary: 'Atualiza um fornecedor existente',
        description: 'Todos os campos são opcionais (sometimes); envie apenas o que deseja alterar.',
        tags: ['Supplier'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'supplier', in: 'path', required: true, description: 'ID do fornecedor', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Distribuidora ABC'),
                    new OA\Property(property: 'responsavel', type: 'string', maxLength: 255, nullable: true, example: 'Maria Souza'),
                    new OA\Property(property: 'cnpj', type: 'string', maxLength: 18, example: '12.345.678/0001-90'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'contato@abc.com'),
                    new OA\Property(property: 'phone', type: 'string', maxLength: 20, nullable: true, example: '(11) 99999-9999'),
                    new OA\Property(property: 'address', type: 'string', maxLength: 255, nullable: true, example: 'Rua das Flores, 123'),
                    new OA\Property(property: 'link_catalog', type: 'string', format: 'uri', maxLength: 255, nullable: true, example: 'https://abc.com/catalogo'),
                    new OA\Property(property: 'description', type: 'string', maxLength: 255, nullable: true, example: 'Fornecedor de eletrônicos'),
                    new OA\Property(property: 'status_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Fornecedor atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Fornecedor atualizado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/SupplierResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Fornecedor não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/v1/supplier/{supplier}',
        operationId: 'deleteSupplier',
        summary: 'Remove um fornecedor',
        tags: ['Supplier'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'supplier', in: 'path', required: true, description: 'ID do fornecedor', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Fornecedor deletado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Fornecedor deletado com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Fornecedor não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(): void
    {
    }
}
