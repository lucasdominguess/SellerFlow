<?php

namespace App\Docs\Swagger\Account;

use OpenApi\Attributes as OA;

// Documentação do CRUD de vínculo usuário-loja (App\Http\Controllers\Accout\UserStoreController).
#[OA\Schema(
    schema: 'UserStoreResource',
    title: 'Vínculo usuário-loja',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'store_id', type: 'integer', example: 1),
        new OA\Property(property: 'status_id', type: 'integer', example: 1),
        new OA\Property(property: 'role_id', type: 'integer', example: 1),
        new OA\Property(property: 'user', ref: '#/components/schemas/UserResource'),
        new OA\Property(property: 'store', ref: '#/components/schemas/StoreResource'),
    ],
    type: 'object'
)]
class UserStoreDocs
{
    #[OA\Get(
        path: '/api/v1/user-store',
        operationId: 'listUserStores',
        summary: 'Lista os vínculos usuário-loja de forma paginada',
        tags: ['User Store'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'perPage', in: 'query', required: false, description: 'Itens por página (1 a 100, padrão 15)', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Página atual (padrão 1)', schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)),
            new OA\Parameter(name: 'user_id', in: 'query', required: false, description: 'Filtro opcional por usuário', schema: new OA\Schema(type: 'integer', example: 1)),
            new OA\Parameter(name: 'store_id', in: 'query', required: false, description: 'Filtro opcional por loja', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'dados recuperados com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'dados recuperados com sucesso'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/UserStoreResource')),
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
        path: '/api/v1/user-store/{user_store}',
        operationId: 'showUserStore',
        summary: 'Exibe um vínculo usuário-loja específico',
        tags: ['User Store'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'user_store', in: 'path', required: true, description: 'ID do vínculo', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'UserStore recuperado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'UserStore recuperado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserStoreResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Vínculo não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/user-store',
        operationId: 'createUserStore',
        summary: 'Cria um vínculo usuário-loja',
        description: 'O par user_id + store_id deve ser único.',
        tags: ['User Store'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['user_id', 'store_id', 'role_id', 'status_id'],
                properties: [
                    new OA\Property(property: 'user_id', type: 'integer', example: 1),
                    new OA\Property(property: 'store_id', type: 'integer', example: 1),
                    new OA\Property(property: 'role_id', type: 'integer', example: 1),
                    new OA\Property(property: 'status_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'UserStore criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'UserStore criado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserStoreResource'),
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
        path: '/api/v1/user-store/{user_store}',
        operationId: 'updateUserStore',
        summary: 'Atualiza um vínculo usuário-loja',
        description: 'Todos os campos são opcionais (sometimes); envie apenas o que deseja alterar.',
        tags: ['User Store'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'user_store', in: 'path', required: true, description: 'ID do vínculo', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'user_id', type: 'integer', example: 1),
                    new OA\Property(property: 'store_id', type: 'integer', example: 1),
                    new OA\Property(property: 'role_id', type: 'integer', example: 1),
                    new OA\Property(property: 'status_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'UserStore atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'UserStore atualizado com sucesso'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserStoreResource'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Vínculo não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/v1/user-store/{user_store}',
        operationId: 'deleteUserStore',
        summary: 'Remove um vínculo usuário-loja',
        tags: ['User Store'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'user_store', in: 'path', required: true, description: 'ID do vínculo', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'UserStore deletado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'UserStore deletado com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autorizado', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Vínculo não encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(): void
    {
    }
}
