<?php

namespace App\Docs\Swagger;

use OpenApi\Attributes as OA;

// Configuração global da documentação OpenAPI/Swagger do SellerFlow.
// Define Info, Server, security scheme (JWT) e os schemas de envelope reutilizáveis.
#[OA\Info(
    version: '1.0.0',
    title: 'SellerFlow API',
    description: 'API REST de gestão para sellers de marketplaces (foco Shopee).'
)]
#[OA\Server(
    url: '/',
    description: 'Servidor base da API'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Autenticação via JWT. Informe o token no formato: Bearer {token}'
)]
// Tags (módulos da API)
#[OA\Tag(name: 'Auth', description: 'Autenticação: login, registro, logout e refresh de token')]
#[OA\Tag(name: 'List Suspended', description: 'Listagens auxiliares para selects (categorias, fornecedores, produtos, etc.)')]
#[OA\Tag(name: 'User', description: 'Gestão de usuários')]
#[OA\Tag(name: 'Store', description: 'Gestão de lojas')]
#[OA\Tag(name: 'User Store', description: 'Vínculo entre usuários e lojas')]
#[OA\Tag(name: 'Company', description: 'Gestão de empresas')]
#[OA\Tag(name: 'Product', description: 'Gestão de produtos')]
#[OA\Tag(name: 'Validate Product', description: 'Validação e cadastro de produtos validados')]
#[OA\Tag(name: 'Supplier', description: 'Gestão de fornecedores')]
#[OA\Tag(name: 'Purchases', description: 'Gestão de compras')]
#[OA\Tag(name: 'Sales', description: 'Gestão de vendas')]
#[OA\Tag(name: 'Stock', description: 'Gestão de estoque, ajustes e indicadores')]
#[OA\Tag(name: 'Finance', description: 'Financeiro: contas a pagar, a receber e fluxo de caixa')]

// ---------------------------------------------------------------------------
// Schemas de envelope reutilizáveis (ApiResponse)
// ---------------------------------------------------------------------------

#[OA\Schema(
    schema: 'PaginationMeta',
    title: 'Meta de paginação',
    properties: [
        new OA\Property(property: 'current_page', type: 'integer', example: 1),
        new OA\Property(property: 'from', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'last_page', type: 'integer', example: 7),
        new OA\Property(property: 'per_page', type: 'integer', example: 15),
        new OA\Property(property: 'to', type: 'integer', nullable: true, example: 15),
        new OA\Property(property: 'total', type: 'integer', example: 100),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ValidationErrorResponse',
    title: 'Erro de validação (422)',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Erro de validação'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string')
            ),
            example: ['email' => ['O campo email é obrigatório.']]
        ),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'UnauthorizedResponse',
    title: 'Não autorizado (401)',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Acesso não autorizado'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'NotFoundResponse',
    title: 'Recurso não encontrado (404)',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Recurso não encontrado'),
    ],
    type: 'object'
)]
class OpenApiConfig
{
}
