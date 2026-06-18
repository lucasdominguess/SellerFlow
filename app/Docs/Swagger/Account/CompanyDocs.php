<?php

namespace App\Docs\Swagger\Account;

use OpenApi\Attributes as OA;

// Documentação do CRUD de empresas (App\Http\Controllers\Accout\CompanyController).
// Reutiliza o schema CompanyResource definido em StoreDocs.
class CompanyDocs
{
    #[OA\Get(
        path: '/api/v1/company',
        operationId: 'listCompanies',
        summary: 'Lista as empresas de forma paginada',
        tags: ['Company'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
            new OA\Parameter(name: 'name', in: 'query', required: false, description: 'Filtro opcional por nome', schema: new OA\Schema(type: 'string', example: 'Loja do João')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Companys recuperados com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Companys recuperados com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/CompanyResource')),
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
        path: '/api/v1/company/{company}',
        operationId: 'showCompany',
        summary: 'Exibe uma empresa específica',
        tags: ['Company'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'company', in: 'path', required: true, description: 'ID da empresa', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Company recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Company recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CompanyResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Empresa não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/company',
        operationId: 'createCompany',
        summary: 'Cria uma nova empresa',
        description: 'O CNPJ é validado e normalizado (somente dígitos) antes de salvar.',
        tags: ['Company'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'cnpj', 'status_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Loja do João LTDA'),
                    new OA\Property(property: 'cnpj', type: 'string', maxLength: 20, description: 'CNPJ válido; aceita formatado ou apenas dígitos', example: '12.345.678/0001-90'),
                    new OA\Property(property: 'description', type: 'string', maxLength: 255, nullable: true, example: 'Loja de eletrônicos'),
                    new OA\Property(property: 'status_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Company criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Company criado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CompanyResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 422, description: 'Erro de validação (inclui CNPJ inválido ou já em uso)', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function store(): void
    {
    }

    #[OA\Put(
        path: '/api/v1/company/{company}',
        operationId: 'updateCompany',
        summary: 'Atualiza uma empresa existente',
        description: 'Todos os campos são opcionais (sometimes); envie apenas o que deseja alterar.',
        tags: ['Company'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'company', in: 'path', required: true, description: 'ID da empresa', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Loja do João LTDA'),
                    new OA\Property(property: 'cnpj', type: 'string', maxLength: 20, description: 'CNPJ válido; aceita formatado ou apenas dígitos', example: '12.345.678/0001-90'),
                    new OA\Property(property: 'description', type: 'string', maxLength: 255, nullable: true, example: 'Loja de eletrônicos'),
                    new OA\Property(property: 'status_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Company atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Company atualizado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CompanyResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Empresa não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/v1/company/{company}',
        operationId: 'deleteCompany',
        summary: 'Remove uma empresa',
        tags: ['Company'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'company', in: 'path', required: true, description: 'ID da empresa', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Company deletado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Company deletado com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Empresa não encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(): void
    {
    }
}
