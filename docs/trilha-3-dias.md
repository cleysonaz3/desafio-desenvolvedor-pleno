# Trilha de Estudo em 3 Dias

Este plano foi feito para você entender a aplicação de forma progressiva, saindo do básico para o uso prático.

Pré-requisito:

- ter lido ao menos a introdução do [Guia Completo para Iniciantes](./guia-iniciante.md).

---

## Dia 1 - Fundamentos e visão do sistema

Objetivo do dia:

- entender o que é back-end, front-end, API, banco e como tudo conversa;
- dominar os conceitos base de HTTP/HTTPS, REST, JSON e OpenAPI/Swagger;
- subir a aplicação e validar que está funcionando.

Teoria (ordem sugerida):

1. Ler no guia iniciante:
- seções 1, 2, 3, 7, 8 e 9.
2. Entender termos-chave:
- endpoint;
- request/response;
- status code;
- autenticação.

Prática:

1. Rodar o projeto:
```bash
./start.sh
```
2. Acessar:
- API status: `http://127.0.0.1:8000/`
- Swagger UI: `http://127.0.0.1:8000/docs`
- Front-end: `http://127.0.0.1:8080`
3. No Swagger:
- testar `POST /api/login`;
- testar `GET /api/me`;
- testar `GET /api/products`.

Checklist do Dia 1:

- consigo explicar diferença entre API e front-end;
- entendi diferença entre HTTP e HTTPS;
- entendi para que serve Swagger/OpenAPI;
- aplicação rodando local com Docker.

---

## Dia 2 - Back-end com Laravel, OO, SOLID e SQL

Objetivo do dia:

- entender como o código Laravel está organizado;
- entender o fluxo de autenticação e regras de negócio;
- entender modelagem do banco e consultas.

Teoria (ordem sugerida):

1. Ler no guia iniciante:
- seções 4, 5, 6, 10, 13, 14 e 15.
2. Ler arquitetura técnica:
- [architecture.md](./architecture.md).

Prática orientada no código:

1. Rotas:
- abrir `projeto-php/routes/api.php`.
2. Autenticação:
- abrir `projeto-php/app/Http/Controllers/Api/AuthController.php`;
- abrir `projeto-php/app/Http/Middleware/AuthenticateJwt.php`;
- abrir `projeto-php/app/Services/JwtService.php`.
3. Produtos:
- abrir `projeto-php/app/Services/ProductService.php`;
- identificar filtros, ordenação e paginação.
4. Banco:
- revisar migrations em `projeto-php/database/migrations/`.

Exercício prático:

1. Adicionar um filtro novo em produtos (exemplo: nome exato ou faixa customizada).
2. Ajustar validação no Form Request.
3. Validar no Swagger.

Checklist do Dia 2:

- consigo explicar papel de Controller, Service, Request e Resource;
- entendi como JWT é gerado e validado;
- consigo explicar o relacionamento entre `categories` e `products`;
- consigo alterar uma regra simples no back-end.

---

## Dia 3 - Front-end, Git e fluxo profissional

Objetivo do dia:

- entender como o front consome a API;
- praticar fluxo de versionamento com Git;
- consolidar o entendimento do sistema ponta a ponta.

Teoria (ordem sugerida):

1. Ler no guia iniciante:
- seções 11, 12, 16, 17, 18, 19 e 20.
2. Revisar segurança aplicada:
- cookie HttpOnly;
- assinatura JWT;
- cuidados para produção (`HTTPS`, segredos, variáveis de ambiente).

Prática:

1. Front-end:
- abrir `projeto-php/frontend/app.js`;
- localizar `request()` e o fluxo de autenticação;
- alterar um texto da interface e validar no navegador.
2. Git:
```bash
git status
git checkout -b estudo/trilha-3-dias
git add .
git commit -m "docs: add 3-day learning track"
```
3. Testes:
```bash
cd projeto-php
php artisan test
```

Checklist do Dia 3:

- entendi como o JavaScript chama a API;
- consigo criar commit pequeno e claro;
- consigo rodar testes e interpretar resultado;
- consigo explicar o fluxo completo: login -> autorização -> CRUD -> persistência.

---

## Resultado esperado ao final dos 3 dias

Você deve conseguir:

- subir e operar o ambiente local;
- testar e navegar na API via Swagger;
- fazer mudanças pequenas no back-end e front-end com segurança;
- versionar e documentar mudanças com Git;
- entender os principais conceitos técnicos usados nesta aplicação.
