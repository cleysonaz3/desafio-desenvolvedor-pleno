# Guia Completo para Iniciantes

Este guia foi escrito para quem está começando e quer entender esta aplicação mesmo sem base prévia nos conceitos usados.

Ele cobre:

- arquitetura;
- design e padrões de projeto;
- PHP;
- Laravel;
- orientação a objetos e SOLID;
- APIs REST;
- Swagger e OpenAPI;
- HTTP, HTTPS e criptografia;
- JavaScript no front-end;
- SQL;
- Docker;
- Git;
- funcionamento real deste projeto.

---

## 1) O que esta aplicação faz

Esta aplicação é um **catálogo de produtos** com:

- autenticação de usuários;
- cadastro, listagem, edição e exclusão de categorias;
- cadastro, listagem, edição e exclusão de produtos;
- filtros e paginação na listagem de produtos;
- documentação da API em `/docs`;
- interface web em outra porta (`8080`) consumindo a API (`8000`).

Em resumo: você tem um sistema com **back-end API + front-end web + banco de dados**.

---

## 2) O que é uma aplicação web em 3 partes

### Front-end

É a interface que roda no navegador (HTML, CSS, JavaScript).  
Neste projeto está em `projeto-php/frontend/`.

### Back-end

É a API que recebe requisições, valida dados, aplica regras e salva no banco.  
Neste projeto está em `projeto-php/` (Laravel/PHP).

### Banco de dados

É onde os dados persistem (MySQL).  
Neste projeto roda em container Docker (`mysql`).

---

## 3) Conceitos fundamentais (sem pular etapa)

## 3.1 O que é framework

Um **framework** é uma base pronta que já resolve partes repetitivas do desenvolvimento.

Exemplo no Laravel:

- roteamento;
- validação;
- acesso ao banco;
- tratamento de erros;
- testes;
- comandos de terminal.

Você foca mais na regra de negócio e menos em “infraestrutura básica”.

## 3.2 O que é PHP

PHP é a linguagem usada no back-end deste projeto.  
Ele executa no servidor e gera respostas HTTP (geralmente JSON neste sistema).

## 3.3 O que é Laravel

Laravel é o framework PHP usado aqui.  
Ele organiza o projeto por convenções:

- `routes/` para rotas;
- `app/Http/Controllers` para controllers;
- `app/Models` para models;
- `database/migrations` para evolução do banco;
- `tests/` para testes.

---

## 4) Orientação a objetos (OO) no contexto do projeto

Orientação a objetos é uma forma de organizar código em estruturas chamadas classes.

Conceitos principais:

- **classe**: molde (ex.: `ProductService`);
- **objeto**: instância de uma classe;
- **método**: função da classe;
- **atributo**: dado da classe;
- **encapsulamento**: esconder detalhes internos e expor só o necessário.

No projeto:

- `AuthService`, `ProductService`, `CategoryService` encapsulam regras;
- controllers apenas coordenam entrada/saída.

---

## 5) SOLID (explicado de forma prática)

SOLID são 5 princípios para código mais fácil de manter.

## S - Single Responsibility Principle

Cada classe deve ter uma responsabilidade principal.

Exemplo:

- controller recebe requisição e responde;
- service aplica regra;
- resource formata saída.

## O - Open/Closed Principle

Código deve estar aberto para extensão e fechado para modificação destrutiva.

Exemplo:

- você adiciona novos filtros em `ProductService` sem reescrever toda a API.

## L - Liskov Substitution Principle

Subtipos não devem quebrar o comportamento esperado do tipo base.

No projeto isso aparece menos explicitamente, mas vale para qualquer herança usada no framework.

## I - Interface Segregation Principle

Evitar contratos “gigantes” que obrigam classes a implementar o que não precisam.

No Laravel, normalmente você mantém classes pequenas e coesas para não cair nesse problema.

## D - Dependency Inversion Principle

Depender de abstrações e injeção de dependências em vez de criar tudo manualmente.

Exemplo:

- `AuthController` recebe `AuthService` no construtor;
- o container do Laravel resolve essa dependência.

---

## 6) Padrões de projeto usados

## MVC (variação pragmática do Laravel)

- Model: entidades e persistência (`app/Models`);
- Controller: entrada e saída HTTP (`app/Http/Controllers`);
- View: no front separado e docs.

## Service Layer

Regras de negócio ficam em `app/Services`, reduzindo lógica nos controllers.

## Form Request

Validação centralizada em classes específicas (`app/Http/Requests/...`).

## API Resource

Transformação de saída JSON em `app/Http/Resources`, mantendo padrão de resposta.

## Active Record (Eloquent)

Models do Laravel representam tabelas e já têm operações de banco embutidas.

---

## 7) API, REST e RESTful

## O que é API

API é uma interface para sistemas conversarem entre si.

Aqui:

- o front chama endpoints da API;
- a API responde JSON.

## O que é REST

REST é um estilo arquitetural para APIs HTTP.

Regras principais usadas aqui:

- recursos com URL clara (`/api/products`);
- verbos HTTP corretos;
- sem estado de sessão no servidor para auth via token/cookie JWT;
- resposta padronizada.

## RESTful

Quando uma API segue bem os princípios REST, dizemos que ela é RESTful.

---

## 8) HTTP, HTTPS e criptografia

## HTTP

Protocolo de comunicação da web.  
Elementos principais:

- método (`GET`, `POST`, `PUT`, `DELETE`);
- URL;
- headers;
- body;
- status code.

## HTTPS

HTTP com segurança por TLS (Transport Layer Security).  
Protege os dados em trânsito contra leitura e alteração por terceiros.

## TLS (criptografia em transporte)

Quando você usa HTTPS:

- cliente e servidor negociam chaves;
- tráfego fica criptografado;
- certificado digital valida identidade do servidor.

## JWT neste projeto: assinatura, não criptografia

O token JWT aqui é assinado com `HS256` (HMAC-SHA256) em `JwtService`.

Isso significa:

- garante integridade (se alguém altera, assinatura quebra);
- não significa que o conteúdo do payload está escondido por criptografia.

---

## 9) Swagger e OpenAPI

## OpenAPI

É a especificação (arquivo/estrutura) que descreve sua API: rotas, parâmetros, respostas, schemas.

Neste projeto:

- JSON em `/docs/openapi.json`.

## Swagger UI

É a interface visual para explorar e testar a API.

Neste projeto:

- disponível em `/docs`.

Resumo:

- OpenAPI = contrato;
- Swagger UI = interface para visualizar/testar contrato.

---

## 10) SQL e banco de dados

## O que é SQL

SQL é a linguagem de banco relacional.

Operações básicas:

- `SELECT` (consulta);
- `INSERT` (inserção);
- `UPDATE` (atualização);
- `DELETE` (remoção).

## Banco deste projeto

MySQL com tabelas principais:

- `users`;
- `categories`;
- `products`.

Relação importante:

- `products.category_id` referencia `categories.id`.

## Índices

Índices aceleram consultas (com custo extra de escrita/armazenamento).  
Aqui existem índices para filtros e ordenação frequentes de produtos.

## Migrations

Migrations versionam o schema do banco em código:

- ficam em `database/migrations`;
- permitem evoluir banco de forma reprodutível.

---

## 11) Docker (do zero para este projeto)

Docker empacota aplicações em containers.

Conceitos:

- **imagem**: “molde”;
- **container**: processo em execução da imagem;
- **volume**: persistência de dados;
- **rede**: comunicação entre containers;
- **compose**: orquestra múltiplos serviços.

Serviços deste projeto em `docker-compose.yml`:

- `app`: Laravel/PHP;
- `nginx`: servidor HTTP da API;
- `frontend`: servidor estático da interface;
- `mysql`: banco de dados.

Portas no host:

- `8000` -> API;
- `8080` -> front-end;
- `3306` -> MySQL.

---

## 12) Git (o essencial para trabalhar no projeto)

Git é controle de versão.

Conceitos:

- **commit**: snapshot das mudanças;
- **branch**: linha de desenvolvimento;
- **merge/rebase**: integração;
- **remote**: repositório remoto (GitHub).

Fluxo básico:

1. `git pull`
2. criar branch
3. alterar código
4. `git add ...`
5. `git commit -m "..."`
6. `git push`
7. abrir Pull Request

---

## 13) Estrutura deste projeto explicada

```text
desafio-desenvolvedor-pleno/
├── README.md
├── docs/
│   ├── README.md
│   ├── guia-iniciante.md
│   └── architecture.md
├── start.sh
└── projeto-php/
    ├── app/
    │   ├── Http/
    │   ├── Models/
    │   ├── Services/
    │   └── Support/
    ├── config/
    ├── database/
    ├── docker/
    ├── frontend/
    ├── routes/
    └── tests/
```

### Mapa rápido de responsabilidades

- `routes/api.php`: define endpoints da API;
- `Controllers`: recebem requisição e retornam resposta;
- `Requests`: validam entrada;
- `Services`: regra de negócio;
- `Resources`: formato de JSON de saída;
- `Models`: acesso a dados (Eloquent);
- `tests/Feature`: testes de integração da API.

---

## 14) Fluxo real de autenticação neste projeto

Existem dois jeitos de autenticar:

## 1. Bearer Token (cliente de API)

- cliente faz login;
- recebe token JWT;
- envia `Authorization: Bearer <token>`.

## 2. Cookie HttpOnly (front-end do projeto)

- front envia header `X-Frontend-Auth: cookie` no login/register;
- API coloca JWT em cookie HttpOnly;
- navegador envia cookie automaticamente;
- middleware lê Bearer ou cookie.

Benefício do cookie HttpOnly:

- JavaScript não acessa o token diretamente, reduzindo risco de vazamento por XSS.

---

## 15) Fluxo real de produtos (listagem com filtros)

`GET /api/products` aceita filtros como:

- `category_id`;
- `min_price`;
- `max_price`;
- `available`;
- `search`;
- `sort_by`;
- `sort_order`;
- `per_page`.

No código:

1. request valida parâmetros;
2. `ProductService` monta query;
3. Eloquent busca no banco;
4. `ProductResource` formata saída;
5. resposta retorna dados + metadados de paginação.

---

## 16) JavaScript e APIs Web usadas no front-end

No arquivo `frontend/app.js`, os principais recursos são:

- `fetch()` para chamadas HTTP;
- `FormData` para leitura de formulários;
- `document.getElementById` para manipular DOM;
- `addEventListener` para eventos;
- `Headers` para controlar headers de requisição;
- `window.scrollTo` para navegação visual;
- `Intl.NumberFormat` para moeda.

O estado da tela é mantido em um objeto `state` no próprio JavaScript.

---

## 17) Como subir e validar localmente

## Fluxo recomendado

Na raiz do repositório:

```bash
./start.sh
```

Esse script:

- sobe containers;
- prepara `.env` se faltar;
- instala dependências PHP se necessário;
- gera `APP_KEY` se faltar;
- roda migrations;
- semeia dados demo;
- mantém logs ativos.

## URLs importantes

- API: `http://127.0.0.1:8000`
- Status: `http://127.0.0.1:8000/`
- Swagger UI: `http://127.0.0.1:8000/docs`
- Front-end: `http://127.0.0.1:8080`

## Credenciais demo

- email: `demo@example.com`
- senha: `password`

---

## 18) Como testar a API rapidamente

## Pelo Swagger UI

1. Abra `/docs`
2. Faça `POST /api/login`
3. Use o token em `Authorize` (Bearer) se estiver testando fluxo token
4. Teste endpoints protegidos

## Pelo front-end

1. Abra `http://127.0.0.1:8080`
2. Faça login com usuário demo
3. Use CRUD de categorias e produtos

---

## 19) Segurança aplicada no projeto

- hash de senha com mecanismos do Laravel;
- autenticação JWT com assinatura HS256;
- expiração de token (`ttl`);
- revogação por `token_version` no usuário;
- validação de entrada via Form Requests;
- cookie HttpOnly para front-end;
- padronização de erros de autenticação/validação.

Importante:

- em produção, `JWT_COOKIE_SECURE` deve ser `true` (somente HTTPS);
- use HTTPS real (certificado válido);
- nunca commitar segredos reais.

---

## 20) Erros comuns e como resolver

## Porta ocupada (`8000`, `8080`, `3306`)

- pare serviços conflitantes;
- ou ajuste portas no `docker-compose.yml`.

## APP_KEY ausente

- rode `php artisan key:generate` (ou use `./start.sh`).

## Erro de banco

- confirme se container `mysql` está saudável;
- confira credenciais de banco no ambiente.

## 401 Unauthorized

- Bearer ausente/inválido/expirado;
- ou cookie não foi enviado corretamente no fluxo front.

---

## 21) Guia de estudo sugerido (se você está no começo)

1. HTTP + JSON + status codes
2. REST + OpenAPI + Swagger
3. Git básico (branch, commit, push, PR)
4. SQL básico (select/insert/update/delete + join + índice)
5. Docker básico (imagem, container, compose)
6. PHP (sintaxe, funções, classes)
7. Laravel (rotas, controllers, requests, models, migrations)
8. Orientação a Objetos + SOLID

Depois disso, releia `docs/architecture.md` para aprofundar.

---

## 22) Resumo executivo

Você está trabalhando em uma API REST em Laravel, com autenticação JWT, banco MySQL, front-end separado, documentação OpenAPI/Swagger e execução via Docker.

Se seguir esta sequência:

- subir com `./start.sh`;
- testar em `/docs` e `:8080`;
- ler este guia + `architecture.md`;

você terá base suficiente para evoluir o projeto com segurança.
