<?php

namespace App\Support;

class OpenApiFactory
{
    /**
     * @return array<string, mixed>
     */
    public function make(): array
    {
        $serverUrl = rtrim((string) config('app.url'), '/');

        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'Catálogo de Produtos API',
                'version' => '1.0.0',
                'description' => 'API REST para autenticação JWT, categorias e produtos da Essential Nutrition.',
            ],
            'servers' => [
                [
                    'url' => $serverUrl,
                ],
            ],
            'tags' => [
                ['name' => 'Auth'],
                ['name' => 'Categories'],
                ['name' => 'Products'],
            ],
            'paths' => [
                '/api/register' => [
                    'post' => [
                        'tags' => ['Auth'],
                        'summary' => 'Registrar usuário',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'required' => ['name', 'email', 'password', 'password_confirmation'],
                                        'properties' => [
                                            'name' => ['type' => 'string', 'example' => 'Cleyson Azevedo'],
                                            'email' => ['type' => 'string', 'format' => 'email', 'example' => 'cleyson@example.com'],
                                            'password' => ['type' => 'string', 'format' => 'password', 'example' => 'secret123'],
                                            'password_confirmation' => ['type' => 'string', 'format' => 'password', 'example' => 'secret123'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '201' => [
                                'description' => 'Usuário registrado com sucesso.',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/AuthResponse',
                                        ],
                                    ],
                                ],
                            ],
                            '422' => $this->validationResponse(),
                        ],
                    ],
                ],
                '/api/login' => [
                    'post' => [
                        'tags' => ['Auth'],
                        'summary' => 'Autenticar usuário',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'required' => ['email', 'password'],
                                        'properties' => [
                                            'email' => ['type' => 'string', 'format' => 'email', 'example' => 'cleyson@example.com'],
                                            'password' => ['type' => 'string', 'format' => 'password', 'example' => 'secret123'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Login realizado com sucesso.',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/AuthResponse',
                                        ],
                                    ],
                                ],
                            ],
                            '422' => $this->validationResponse(),
                        ],
                    ],
                ],
                '/api/logout' => [
                    'post' => [
                        'tags' => ['Auth'],
                        'summary' => 'Revogar token atual',
                        'security' => [
                            ['BearerAuth' => []],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Logout realizado com sucesso.',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'message' => ['type' => 'string', 'example' => 'Logout realizado com sucesso.'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '401' => $this->unauthorizedResponse(),
                        ],
                    ],
                ],
                '/api/me' => [
                    'get' => [
                        'tags' => ['Auth'],
                        'summary' => 'Obter usuário autenticado',
                        'security' => [
                            ['BearerAuth' => []],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Usuário autenticado.',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data' => ['$ref' => '#/components/schemas/User'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '401' => $this->unauthorizedResponse(),
                        ],
                    ],
                ],
                '/api/categories' => [
                    'get' => [
                        'tags' => ['Categories'],
                        'summary' => 'Listar categorias',
                        'security' => [
                            ['BearerAuth' => []],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Lista de categorias.',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data' => [
                                                    'type' => 'array',
                                                    'items' => ['$ref' => '#/components/schemas/Category'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '401' => $this->unauthorizedResponse(),
                        ],
                    ],
                    'post' => [
                        'tags' => ['Categories'],
                        'summary' => 'Criar categoria',
                        'security' => [
                            ['BearerAuth' => []],
                        ],
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'required' => ['name'],
                                        'properties' => [
                                            'name' => ['type' => 'string', 'example' => 'Suplementos'],
                                            'description' => ['type' => 'string', 'nullable' => true, 'example' => 'Produtos para performance e saúde.'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '201' => [
                                'description' => 'Categoria criada com sucesso.',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data' => ['$ref' => '#/components/schemas/Category'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '401' => $this->unauthorizedResponse(),
                            '422' => $this->validationResponse(),
                        ],
                    ],
                ],
                '/api/categories/{id}' => [
                    'get' => [
                        'tags' => ['Categories'],
                        'summary' => 'Detalhar categoria',
                        'security' => [
                            ['BearerAuth' => []],
                        ],
                        'parameters' => [
                            $this->idParameter(),
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Categoria encontrada.',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data' => ['$ref' => '#/components/schemas/Category'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '401' => $this->unauthorizedResponse(),
                            '404' => $this->notFoundResponse(),
                        ],
                    ],
                    'put' => [
                        'tags' => ['Categories'],
                        'summary' => 'Atualizar categoria',
                        'security' => [
                            ['BearerAuth' => []],
                        ],
                        'parameters' => [
                            $this->idParameter(),
                        ],
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'name' => ['type' => 'string', 'example' => 'Vitaminas'],
                                            'description' => ['type' => 'string', 'nullable' => true, 'example' => 'Linha atualizada.'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Categoria atualizada com sucesso.',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data' => ['$ref' => '#/components/schemas/Category'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '401' => $this->unauthorizedResponse(),
                            '404' => $this->notFoundResponse(),
                            '422' => $this->validationResponse(),
                        ],
                    ],
                    'delete' => [
                        'tags' => ['Categories'],
                        'summary' => 'Remover categoria',
                        'security' => [
                            ['BearerAuth' => []],
                        ],
                        'parameters' => [
                            $this->idParameter(),
                        ],
                        'responses' => [
                            '204' => [
                                'description' => 'Categoria removida com sucesso.',
                            ],
                            '401' => $this->unauthorizedResponse(),
                            '404' => $this->notFoundResponse(),
                        ],
                    ],
                ],
                '/api/products' => [
                    'get' => [
                        'tags' => ['Products'],
                        'summary' => 'Listar produtos com filtros',
                        'security' => [
                            ['BearerAuth' => []],
                        ],
                        'parameters' => [
                            ['name' => 'category_id', 'in' => 'query', 'schema' => ['type' => 'integer']],
                            ['name' => 'min_price', 'in' => 'query', 'schema' => ['type' => 'number', 'format' => 'float']],
                            ['name' => 'max_price', 'in' => 'query', 'schema' => ['type' => 'number', 'format' => 'float']],
                            ['name' => 'available', 'in' => 'query', 'schema' => ['type' => 'boolean']],
                            ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string']],
                            ['name' => 'sort_by', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['name', 'price', 'created_at']]],
                            ['name' => 'sort_order', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['asc', 'desc']]],
                            ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer', 'default' => 15]],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Lista paginada de produtos.',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data' => [
                                                    'type' => 'array',
                                                    'items' => ['$ref' => '#/components/schemas/Product'],
                                                ],
                                                'links' => ['type' => 'object'],
                                                'meta' => ['type' => 'object'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '401' => $this->unauthorizedResponse(),
                            '422' => $this->validationResponse(),
                        ],
                    ],
                    'post' => [
                        'tags' => ['Products'],
                        'summary' => 'Criar produto',
                        'security' => [
                            ['BearerAuth' => []],
                        ],
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'required' => ['category_id', 'name', 'price'],
                                        'properties' => [
                                            'category_id' => ['type' => 'integer', 'example' => 1],
                                            'name' => ['type' => 'string', 'example' => 'Whey Protein'],
                                            'description' => ['type' => 'string', 'nullable' => true, 'example' => 'Proteína concentrada.'],
                                            'image_url' => ['type' => 'string', 'format' => 'uri', 'nullable' => true, 'example' => 'https://example.com/produtos/whey.png'],
                                            'price' => ['type' => 'number', 'format' => 'float', 'example' => 149.90],
                                            'available' => ['type' => 'boolean', 'example' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '201' => [
                                'description' => 'Produto criado com sucesso.',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data' => ['$ref' => '#/components/schemas/Product'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '401' => $this->unauthorizedResponse(),
                            '422' => $this->validationResponse(),
                        ],
                    ],
                ],
                '/api/products/{id}' => [
                    'get' => [
                        'tags' => ['Products'],
                        'summary' => 'Detalhar produto',
                        'security' => [
                            ['BearerAuth' => []],
                        ],
                        'parameters' => [
                            $this->idParameter(),
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Produto encontrado.',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data' => ['$ref' => '#/components/schemas/Product'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '401' => $this->unauthorizedResponse(),
                            '404' => $this->notFoundResponse(),
                        ],
                    ],
                    'put' => [
                        'tags' => ['Products'],
                        'summary' => 'Atualizar produto',
                        'security' => [
                            ['BearerAuth' => []],
                        ],
                        'parameters' => [
                            $this->idParameter(),
                        ],
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'category_id' => ['type' => 'integer', 'example' => 1],
                                            'name' => ['type' => 'string', 'example' => 'Whey Protein Isolado'],
                                            'description' => ['type' => 'string', 'nullable' => true, 'example' => 'Versão atualizada.'],
                                            'image_url' => ['type' => 'string', 'format' => 'uri', 'nullable' => true, 'example' => 'https://example.com/produtos/whey-isolado.png'],
                                            'price' => ['type' => 'number', 'format' => 'float', 'example' => 169.90],
                                            'available' => ['type' => 'boolean', 'example' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Produto atualizado com sucesso.',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data' => ['$ref' => '#/components/schemas/Product'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '401' => $this->unauthorizedResponse(),
                            '404' => $this->notFoundResponse(),
                            '422' => $this->validationResponse(),
                        ],
                    ],
                    'delete' => [
                        'tags' => ['Products'],
                        'summary' => 'Remover produto',
                        'security' => [
                            ['BearerAuth' => []],
                        ],
                        'parameters' => [
                            $this->idParameter(),
                        ],
                        'responses' => [
                            '204' => [
                                'description' => 'Produto removido com sucesso.',
                            ],
                            '401' => $this->unauthorizedResponse(),
                            '404' => $this->notFoundResponse(),
                        ],
                    ],
                ],
            ],
            'components' => [
                'securitySchemes' => [
                    'BearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT',
                    ],
                ],
                'schemas' => [
                    'User' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'name' => ['type' => 'string', 'example' => 'Cleyson Azevedo'],
                            'email' => ['type' => 'string', 'example' => 'cleyson@example.com'],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'AuthResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => ['type' => 'string', 'example' => 'Login realizado com sucesso.'],
                            'data' => [
                                'type' => 'object',
                                'properties' => [
                                    'token' => ['type' => 'string', 'nullable' => true],
                                    'token_type' => ['type' => 'string', 'example' => 'Bearer'],
                                    'expires_in' => ['type' => 'integer', 'example' => 3600],
                                    'user' => ['$ref' => '#/components/schemas/User'],
                                ],
                            ],
                        ],
                    ],
                    'Category' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'name' => ['type' => 'string', 'example' => 'Suplementos'],
                            'description' => ['type' => 'string', 'nullable' => true],
                            'products_count' => ['type' => 'integer', 'nullable' => true, 'example' => 12],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'Product' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'category_id' => ['type' => 'integer', 'example' => 1],
                            'category' => ['$ref' => '#/components/schemas/Category'],
                            'name' => ['type' => 'string', 'example' => 'Whey Protein'],
                            'description' => ['type' => 'string', 'nullable' => true],
                            'image_url' => ['type' => 'string', 'format' => 'uri', 'nullable' => true],
                            'price' => ['type' => 'string', 'example' => '149.90'],
                            'available' => ['type' => 'boolean', 'example' => true],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'Error' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => ['type' => 'string'],
                        ],
                    ],
                    'ValidationError' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => ['type' => 'string', 'example' => 'Dados inválidos.'],
                            'errors' => [
                                'type' => 'object',
                                'additionalProperties' => [
                                    'type' => 'array',
                                    'items' => ['type' => 'string'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function idParameter(): array
    {
        return [
            'name' => 'id',
            'in' => 'path',
            'required' => true,
            'schema' => [
                'type' => 'integer',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validationResponse(): array
    {
        return [
            'description' => 'Erro de validação.',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/ValidationError',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function unauthorizedResponse(): array
    {
        return [
            'description' => 'Não autenticado.',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/Error',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function notFoundResponse(): array
    {
        return [
            'description' => 'Recurso não encontrado.',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/Error',
                    ],
                ],
            ],
        ];
    }
}
