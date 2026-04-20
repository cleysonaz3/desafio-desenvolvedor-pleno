# Arquitetura Proposta

## Objetivo

Transformar a base atual em uma API REST para catálogo de produtos da Essential Nutrition, cobrindo:

- autenticação com JWT;
- CRUD de categorias;
- CRUD de produtos;
- filtros, ordenação e paginação;
- respostas JSON padronizadas;
- testes automatizados;
- documentação operacional e de contribuição.

## Princípios

- Controllers enxutos e sem regra de negócio.
- Validação centralizada em Form Requests.
- Serialização padronizada com API Resources.
- Regras de negócio e consultas complexas em Services.
- Models responsáveis apenas por relacionamento, casts, escopos simples e proteção de atributos.
- Separação explícita entre rotas web e API.
- Erros previsíveis e consistentes em JSON.

## Visão em Camadas

### 1. Interface HTTP

Responsável por entrada e saída da API.

- `routes/api.php`
- `app/Http/Controllers/Api`
- `app/Http/Requests`
- `app/Http/Resources`

Responsabilidades:

- receber a requisição;
- validar os dados;
- acionar o service correto;
- devolver resposta JSON com status HTTP adequado.

### 2. Aplicação

Responsável por orquestrar os casos de uso.

- `app/Services/AuthService.php`
- `app/Services/CategoryService.php`
- `app/Services/ProductService.php`

Responsabilidades:

- criar, atualizar e remover entidades;
- encapsular busca com filtros;
- centralizar regras de autenticação e emissão/invalidação de token;
- evitar duplicação de regra entre controllers.

### 3. Domínio / Modelo

Responsável pela estrutura das entidades e regras mais próximas dos dados.

- `app/Models/User.php`
- `app/Models/Category.php`
- `app/Models/Product.php`

Responsabilidades:

- relacionamentos Eloquent;
- `fillable`, `casts` e escopos simples;
- proteção contra mass assignment;
- apoio a consultas legíveis.

### 4. Infraestrutura

Responsável por persistência, execução local e entrega.

- `database/migrations`
- `database/seeders`
- `database/factories`
- `docker-compose.yml`
- `Dockerfile`
- `docker/nginx/default.conf`

Responsabilidades:

- manter schema consistente;
- suportar ambiente local e Docker;
- prover dados de teste e seed inicial.

## Estrutura Recomendada

```text
projeto-php/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── CategoryController.php
│   │   │       └── ProductController.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── Category/
│   │   │   │   ├── StoreCategoryRequest.php
│   │   │   │   └── UpdateCategoryRequest.php
│   │   │   └── Product/
│   │   │       ├── IndexProductRequest.php
│   │   │       ├── StoreProductRequest.php
│   │   │       └── UpdateProductRequest.php
│   │   └── Resources/
│   │       ├── CategoryResource.php
│   │       ├── ProductResource.php
│   │       └── AuthResource.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   └── Product.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── CategoryService.php
│   │   └── ProductService.php
│   └── Exceptions/
│       └── Handler.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── routes/
│   ├── api.php
│   └── web.php
├── tests/
│   └── Feature/
│       ├── Auth/
│       ├── Categories/
│       └── Products/
└── docs/
    └── architecture.md
```

## Fluxo de Requisição

### Autenticação

1. `POST /api/register` recebe os dados.
2. `RegisterRequest` valida nome, email e senha.
3. `AuthService` cria o usuário e gera o token JWT.
4. `AuthResource` devolve usuário autenticado e token.

### Produtos

1. `GET /api/products` recebe filtros e paginação.
2. `IndexProductRequest` valida parâmetros de query string.
3. `ProductService` monta a query com filtros, busca textual e ordenação.
4. `ProductResource` serializa cada item.
5. A resposta retorna coleção paginada com metadados.

## Contratos da API

### Rotas públicas

- `POST /api/register`
- `POST /api/login`

### Rotas protegidas por JWT

- `GET /api/me`
- `POST /api/logout`
- `GET /api/categories`
- `POST /api/categories`
- `GET /api/categories/{id}`
- `PUT /api/categories/{id}`
- `DELETE /api/categories/{id}`
- `GET /api/products`
- `POST /api/products`
- `GET /api/products/{id}`
- `PUT /api/products/{id}`
- `DELETE /api/products/{id}`

## Regras por Componente

### Controllers

- não acessam `Request::all()`;
- não contêm query complexa;
- não formatam resposta manualmente quando existir Resource;
- não conhecem detalhes de persistência além do contrato do service.

### Form Requests

- validam payload e query params;
- centralizam mensagens de validação;
- podem normalizar filtros em métodos auxiliares.

### Services

- recebem dados já validados;
- encapsulam transações quando necessário;
- concentram filtros, ordenação e regras de negócio;
- devolvem models ou paginadores para a camada HTTP serializar.

### Resources

- expõem apenas campos necessários;
- escondem dados sensíveis;
- padronizam payload de sucesso.

## Modelagem de Dados

### `users`

- `id`
- `name`
- `email` com índice único
- `password`
- `token_version` (controle de invalidação de token)
- `remember_token`
- timestamps

### `categories`

- `id`
- `name`
- `description` nullable
- timestamps

### `products`

- `id`
- `category_id` com foreign key e índice
- `name`
- `description` nullable
- `image_url` nullable
- `showcase_tone` nullable
- `showcase_caption` nullable
- `price` decimal `(10,2)`
- `available` boolean default `true`
- timestamps

## Ajustes Aplicados de Banco

- índice composto em `products(category_id, available)`;
- índice em `products.price`;
- índice em `products.created_at`;
- índice em `products.name`;
- validação de unicidade de email;
- `cascadeOnDelete()` entre categoria e produtos;
- factories para `User`, `Category` e `Product`.

## Segurança

- JWT obrigatório em rotas protegidas;
- senhas com hash via Laravel;
- validação de entrada via Form Requests;
- proteção de atributos com `fillable`;
- tratamento uniforme para `404`, `401`, `422` e `500`;
- resposta JSON sem stack trace em produção.

## Estratégia de Erros

Formato recomendado:

```json
{
  "message": "Dados inválidos.",
  "errors": {
    "price": [
      "O campo price deve ser maior que zero."
    ]
  }
}
```

Padronizações mínimas:

- `200 OK` para leitura;
- `201 Created` para criação;
- `204 No Content` para remoção;
- `401 Unauthorized` para token ausente ou inválido;
- `404 Not Found` para recurso inexistente;
- `422 Unprocessable Entity` para validação;
- `500 Internal Server Error` para falhas inesperadas.

## Estratégia de Testes

### Feature Tests

- autenticação: registro, login, logout e acesso protegido;
- categorias: CRUD completo e validações;
- produtos: CRUD completo, filtros, ordenação e paginação;
- respostas de erro padronizadas.

### Dados de teste

- factories para geração de usuários, categorias e produtos;
- seeders apenas para ambiente local e demonstração;
- testes independentes usando refresh do banco.

## Sequência Recomendada de Implementação

1. Ajustar `bootstrap/app.php` para carregar `routes/api.php`.
2. Instalar e configurar JWT.
3. Criar `AuthController`, `AuthService` e requests de autenticação.
4. Mover controllers atuais para `app/Http/Controllers/Api`.
5. Criar Form Requests e Resources para categorias e produtos.
6. Criar `CategoryService` e `ProductService`.
7. Padronizar exceptions e respostas JSON.
8. Implementar filtros de produtos.
9. Criar factories, seeders e testes de integração.
10. Atualizar README com exemplos reais de request/response.

## Situação Atual

A base já foi evoluída para essa arquitetura, com:

- `routes/api.php` carregado no bootstrap;
- controllers da API em `app/Http/Controllers/Api`;
- autenticação JWT própria com middleware dedicado;
- Form Requests, API Resources e Services;
- Swagger UI em `/docs` e OpenAPI JSON em `/docs/openapi.json`;
- testes de feature para autenticação, categorias e produtos.

Os arquivos web antigos podem permanecer no repositório como resquício da versão inicial, mas a interface principal do projeto agora é a API REST documentada via OpenAPI JSON.
