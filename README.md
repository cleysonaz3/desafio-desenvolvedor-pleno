# API REST - Catálogo de Produtos

Repositório do desafio técnico para construção de uma API REST de catálogo de produtos da Essential Nutrition com Laravel.

## Status Atual

O projeto agora contém a implementação principal da API:

- autenticação JWT com expiração e revogação por `token_version`;
- CRUD REST de categorias;
- CRUD REST de produtos;
- filtros, ordenação e paginação em produtos;
- Form Requests, API Resources e Services;
- respostas JSON padronizadas para validação, autenticação e recursos inexistentes;
- documentação interativa em Scalar API Reference.

## Arquitetura

A arquitetura proposta para a evolução do projeto está documentada em [docs/architecture.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/architecture.md).

Essa arquitetura cobre:

- separação entre camadas HTTP, aplicação, domínio e infraestrutura;
- controllers enxutos;
- validação via Form Requests;
- serialização via API Resources;
- regras de negócio em Services;
- autenticação JWT;
- testes de feature para auth, categorias e produtos.

## Scalar

As rotas estão disponíveis pela interface Scalar em:

- `/docs`
- `/swagger`
- `/scalar`

O documento OpenAPI em JSON está disponível em:

- `/docs/openapi.json`

## Status

A rota raiz `/` retorna o status do projeto em JSON com:

- versão da aplicação;
- status geral;
- saúde da aplicação;
- saúde do banco de dados;
- link para a documentação em `/docs`.

## Estrutura Atual do Repositório

```text
.
├── CONTRIBUTING.md
├── README.md
├── docs/
│   └── architecture.md
└── projeto-php/
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── docker/
    ├── public/
    ├── resources/
    ├── routes/
    ├── tests/
    ├── Dockerfile
    ├── composer.json
    └── docker-compose.yml
```

## Requisitos

- Docker e Docker Compose; ou
- PHP 8.3, Composer e MySQL 8.0 para execução local.

## Subindo o Ambiente

### Docker

```bash
cd projeto-php
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

### Local

```bash
cd projeto-php
composer install
cp .env.example .env
php artisan key:generate
# Configure o banco de dados no .env
php artisan migrate
php artisan serve
```

## Principais Endpoints

- `POST /api/register`
- `POST /api/login`
- `POST /api/logout`
- `GET|POST|PUT|DELETE /api/categories`
- `GET|POST|PUT|DELETE /api/products`

## Licença

Este projeto faz parte de um desafio técnico do Essentia Group.
