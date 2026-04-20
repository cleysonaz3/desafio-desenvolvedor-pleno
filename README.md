

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

<img width="1230" height="856" alt="image" src="https://github.com/user-attachments/assets/71af3c66-c480-4d9d-a074-38bf0b02244f" />
<img width="1230" height="856" alt="image" src="https://github.com/user-attachments/assets/cbd28334-394a-404a-a625-f3387f48d7ee" />
<img width="1230" height="856" alt="image" src="https://github.com/user-attachments/assets/6b099702-8eeb-4c87-a43c-0a74cbc5a88c" />
<img width="1230" height="856" alt="image" src="https://github.com/user-attachments/assets/1b2ec572-3299-4a0d-8014-a52315a33ea9" />
<img width="1230" height="856" alt="image" src="https://github.com/user-attachments/assets/dca8e764-832d-44cd-9421-900e0f6e3874" />







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
- `13 passed`
- `96 assertions`
