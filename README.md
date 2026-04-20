# API REST - Catálogo de Produtos

Repositório do desafio técnico para construção de uma API REST de catálogo de produtos da Essential Nutrition com Laravel.

## Entrega Atual

O projeto atende ao escopo principal do teste:

- autenticação JWT na API;
- CRUD completo de categorias;
- CRUD completo de produtos;
- filtros, ordenação e paginação;
- Swagger UI e OpenAPI JSON;
- front-end separado em outra porta;
- testes automatizados cobrindo auth, categorias, produtos e fluxo E2E.

## Subindo o Ambiente

Fluxo recomendado:

```bash
./start.sh
```

URLs principais:

- API: `http://localhost:8000`
- Status: `http://localhost:8000/`
- Swagger UI: `http://localhost:8000/docs`
- Front-end: `http://localhost:8080`

Manual:

```bash
cd projeto-php
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan demo:seed-api
```

## Credenciais Demo

- email: `demo@example.com`
- senha: `password`

## Estrutura

```text
.
├── docs/
│   └── architecture.md
├── projeto-php/
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── docker/
│   ├── frontend/
│   ├── resources/
│   ├── routes/
│   └── tests/
└── start.sh
```

## Documentação

- README da aplicação: [projeto-php/README.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/projeto-php/README.md)
- Arquitetura: [docs/architecture.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/architecture.md)

## Testes

Última validação executada:

- `php artisan test`
- `12 passed`
- `90 assertions`
