# Projeto Laravel

Aplicação Laravel 13 que servirá como base para a API REST de catálogo de produtos do desafio técnico.

## Objetivo da Aplicação

Implementar uma API com:

- autenticação JWT;
- CRUD de categorias;
- CRUD de produtos;
- filtros por categoria, preço, disponibilidade e texto;
- paginação;
- respostas JSON padronizadas;
- testes automatizados.

## Estado Atual

Atualmente a aplicação possui:

- rota `/` com status e versão do projeto;
- front-end separado em `frontend/`, servido em outra porta;
- `routes/api.php` com autenticação e CRUDs REST;
- controllers em `app/Http/Controllers/Api`;
- Form Requests para autenticação, categorias e produtos;
- API Resources para serialização;
- Services para autenticação e regras de negócio;
- JWT próprio com assinatura HS256;
- testes de feature cobrindo autenticação, categorias e produtos;
- Scalar API Reference em `/docs`, `/swagger` e `/scalar`.

## Arquitetura Proposta

Consulte a arquitetura alvo em [../docs/architecture.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/architecture.md).

## Execução

### Com Docker

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

Aplicações disponíveis:

- API: `http://localhost:8000`
- Front-end: `http://localhost:8080`

### Local

```bash
php ./composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Dados de Demonstração

Para preparar um fluxo manual completo no Scalar:

```bash
php artisan demo:seed-api
```

Ou recriando as tabelas antes:

```bash
php artisan demo:seed-api --fresh
```

Credenciais geradas para teste manual:

- usuário: `demo@example.com`
- senha: `password`

Fluxo sugerido:

1. Abra `/docs`.
2. Faça `POST /api/login`.
3. Copie o token retornado.
4. Autorize no Scalar com `Bearer <token>`.
5. Teste os CRUDs de categorias e produtos.

## Front-end

O front-end foi implementado para atender ao escopo prático da vaga e do desafio:

- autenticação por JWT;
- CRUD de categorias;
- CRUD de produtos;
- filtros de catálogo;
- monitor de saúde da aplicação;
- interface separada da API em outra porta.

Arquivos principais:

```text
frontend/
├── index.html
├── styles.css
├── app.js
└── favicon.svg
```

No Docker, o container `frontend` publica a aplicação em `http://localhost:8080`
e faz proxy para a API Laravel internamente.

## Estrutura Implementada

```text
app/
├── Http/
│   ├── Controllers/Api/
│   ├── Requests/
│   └── Resources/
├── Models/
└── Services/

routes/
├── api.php
└── web.php

tests/
└── Feature/
```

## Critérios de Implementação

- seguir PSR-12;
- manter controllers enxutos;
- concentrar regras de negócio em services;
- usar Form Requests para validação;
- usar API Resources para resposta;
- proteger rotas com JWT;
- documentar endpoints e evidências de testes.
