<?php

namespace App\Docs\Swagger\Account;

use OpenApi\Attributes as OA;

// Documentação do CRUD de usuários (App\Http\Controllers\Accout\UserController).
#[OA\Schema(
    schema: 'UserResource',
    title: 'Usuário',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'João Seller'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'seller@sellerflow.com'),
        new OA\Property(property: 'status_id', type: 'integer', example: 1),
    ],
    type: 'object'
)]
class UserDocs
{
    #[OA\Get(
        path: '/api/v1/user',
        operationId: 'listUsers',
        summary: 'Lista os usuários de forma paginada',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
            new OA\Parameter(name: 'name', in: 'query', required: false, description: 'Filtro opcional por nome', schema: new OA\Schema(type: 'string', example: 'João')),
            new OA\Parameter(name: 'email', in: 'query', required: false, description: 'Filtro opcional por email', schema: new OA\Schema(type: 'string', example: 'seller@sellerflow.com')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuarios recuperados com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Usuarios recuperados com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/UserResource')),
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
        path: '/api/v1/user/{user}',
        operationId: 'showUser',
        summary: 'Exibe um usuário específico',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'ID do usuário', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuario recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Usuario recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Usuário não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/user',
        operationId: 'createUser',
        summary: 'Cria um novo usuário',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'confirmed_password', 'status_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'João Seller'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'seller@sellerflow.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'senha1234'),
                    new OA\Property(property: 'confirmed_password', type: 'string', format: 'password', minLength: 8, description: 'Deve ser igual a password', example: 'senha1234'),
                    new OA\Property(property: 'status_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuario criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Usuario criado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserResource'),
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
        path: '/api/v1/user/{user}',
        operationId: 'updateUser',
        summary: 'Atualiza um usuário existente',
        description: 'Todos os campos são opcionais (sometimes); envie apenas o que deseja alterar.',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'ID do usuário', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'João Seller'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'seller@sellerflow.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'senha1234'),
                    new OA\Property(property: 'status_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuario atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Usuario atualizado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Usuário não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/v1/user/{user}',
        operationId: 'deleteUser',
        summary: 'Remove um usuário',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'ID do usuário', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuario deletado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Usuario deletado com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Usuário não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(): void
    {
    }
}
