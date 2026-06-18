<?php

namespace App\Docs\Swagger\Auth;

use OpenApi\Attributes as OA;

// Documentação dos endpoints de autenticação (App\Http\Controllers\Auth\AuthController).
class AuthDocs
{
    #[OA\Post(
        path: '/api/v1/auth/login',
        operationId: 'authLogin',
        summary: 'Autentica o usuário e retorna o token JWT',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'seller@sellerflow.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'senha1234'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login realizado com sucesso. O token também é retornado no header Authorization e em cookie httpOnly.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Login realizado com sucesso'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'),
                                new OA\Property(property: 'name', type: 'string', example: 'João Seller'),
                                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'seller@sellerflow.com'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Credenciais inválidas', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function login(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/auth/register',
        operationId: 'authRegister',
        summary: 'Registra um novo usuário e sua empresa',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'confirm_password', 'company_name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'João Seller'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'seller@sellerflow.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'senha1234'),
                    new OA\Property(property: 'confirm_password', type: 'string', format: 'password', minLength: 8, description: 'Deve ser igual a password', example: 'senha1234'),
                    new OA\Property(property: 'company_name', type: 'string', maxLength: 255, example: 'Loja do João LTDA'),
                    new OA\Property(property: 'cnpj', type: 'string', maxLength: 18, nullable: true, example: '12.345.678/0001-90'),
                    new OA\Property(property: 'description', type: 'string', maxLength: 255, nullable: true, example: 'Loja de eletrônicos'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuário registrado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário registrado com sucesso'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'name', type: 'string', example: 'João Seller'),
                                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'seller@sellerflow.com'),
                                new OA\Property(property: 'status_id', type: 'integer', example: 1),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Erro de validação', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function register(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/auth/logout',
        operationId: 'authLogout',
        summary: 'Invalida o token atual e encerra a sessão',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout realizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Logout realizado com sucesso'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Token ausente ou inválido', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
        ]
    )]
    public function logout(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/auth/refresh',
        operationId: 'authRefresh',
        summary: 'Gera um novo token JWT a partir do token atual',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token atualizado com sucesso. O novo token também volta no header Authorization e em cookie httpOnly.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Token atualizado com sucesso'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Token ausente ou inválido', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
        ]
    )]
    public function refreshToken(): void
    {
    }
}
