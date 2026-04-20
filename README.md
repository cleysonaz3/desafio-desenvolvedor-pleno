

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

- 
<img width="1830" height="848" alt="image" src="https://github.com/user-attachments/assets/8ceb8c46-d366-46da-a430-a1be634c02e1" />



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

- Índice da documentação: [docs/README.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/README.md)
- Guia completo para iniciantes: [docs/guia-iniciante.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/guia-iniciante.md)
- Trilha de estudo em 3 dias: [docs/trilha-3-dias.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/trilha-3-dias.md)
- README da aplicação: [projeto-php/README.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/projeto-php/README.md)
- Arquitetura técnica: [docs/architecture.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/architecture.md)

## Testes

Última validação executada:

- `php artisan test`
- `12 passed`
- `90 assertions`
