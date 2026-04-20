# API REST - Catálogo de Produtos

API para gerenciamento do catálogo de produtos da Essential Nutrition, desenvolvida com Laravel 13.

## Leitura para iniciantes

Se você ainda está aprendendo conceitos como arquitetura, REST, HTTP/HTTPS, JWT, OpenAPI/Swagger, SQL, Docker, Git, orientação a objetos e SOLID, comece por:

- [../docs/README.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/README.md)
- [../docs/guia-iniciante.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/guia-iniciante.md)
- [../docs/trilha-3-dias.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/trilha-3-dias.md)
- [../docs/architecture.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/architecture.md)

## Escopo Entregue

- autenticação JWT para a API;
- fluxo seguro de autenticação no front com cookie `HttpOnly`;
- CRUD de categorias;
- CRUD de produtos;
- filtros por categoria, faixa de preço, disponibilidade e texto;
- ordenação e paginação;
- documentação via Swagger UI em `/docs`;
- front-end separado em outra porta;
- testes automatizados de feature e fluxo E2E.

## Tecnologias

- PHP 8.3
- Laravel 13
- MySQL 8.0
- Nginx
- Docker / Docker Compose
- JWT
- Bootstrap 5

## Execução

### Com Docker

Fluxo recomendado para avaliação:

```bash
../start.sh
```

O script:

- sobe `mysql`, `app`, `nginx` e `frontend`;
- garante `.env`, `APP_KEY`, migrations e dados demo;
- mantém os logs em foreground;
- encerra os containers com `Ctrl+C`.

Ou manualmente:

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan demo:seed-api
```

URLs:

- API: `http://localhost:8000`
- Status: `http://localhost:8000/`
- Swagger UI: `http://localhost:8000/docs`
- OpenAPI JSON: `http://localhost:8000/docs/openapi.json`
- Front-end: `http://localhost:8080`

### Local

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure o banco no .env
php artisan migrate
php artisan serve
```

## Usuário Demo

- email: `demo@example.com`
- senha: `password`

## Endpoints

### Autenticação

- `POST /api/register`
- `POST /api/login`
- `GET /api/me`
- `POST /api/logout`

### Categorias

- `GET /api/categories`
- `POST /api/categories`
- `GET /api/categories/{id}`
- `PUT /api/categories/{id}`
- `DELETE /api/categories/{id}`

### Produtos

- `GET /api/products`
- `POST /api/products`
- `GET /api/products/{id}`
- `PUT /api/products/{id}`
- `DELETE /api/products/{id}`

Filtros suportados em `GET /api/products`:

- `category_id`
- `min_price`
- `max_price`
- `available`
- `search`
- `sort_by`
- `sort_order`
- `per_page`

## Modelagem

### `users`

- `id`
- `name`
- `email` único
- `password`
- `token_version`
- timestamps

### `categories`

- `id`
- `name`
- `description`
- timestamps

### `products`

- `id`
- `category_id`
- `name`
- `description`
- `image_url`
- `price`
- `available`
- timestamps

Índices aplicados para suportar os filtros e ordenações mais frequentes:

- `products(category_id, available)`
- `products(price)`
- `products(created_at)`
- `products(name)`

## Arquitetura

Estrutura implementada:

```text
app/
├── Http/
│   ├── Controllers/Api/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Services/
└── Support/

database/
├── factories/
├── migrations/
└── seeders/

routes/
├── api.php
└── web.php

tests/
└── Feature/
```

Detalhamento em [../docs/architecture.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/architecture.md).

## Segurança

- rotas protegidas por JWT;
- senhas com hash do Laravel;
- Form Requests para validação de entrada;
- `fillable` e `casts` nos models;
- erros `401`, `404`, `422` e `500` padronizados em JSON;
- front-end sem persistir JWT em `localStorage`;
- autenticação do painel via cookie `HttpOnly`.

## Testes

```bash
php artisan test
```

Última validação:

- `12 passed`
- `90 assertions`

Cobertura atual:

- autenticação
- categorias
- produtos
- fluxo E2E completo da API

## Front-end

O front-end fica em `frontend/` e atende ao escopo funcional pedido na vaga:

- login e registro;
- autenticação antes do acesso ao painel;
- CRUD de categorias;
- CRUD de produtos;
- filtros e vitrine visual do catálogo.
