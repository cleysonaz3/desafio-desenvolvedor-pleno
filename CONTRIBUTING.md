# Como Contribuir

## Processo de Entrega

### 1. Fork e Clone
```bash
git clone https://github.com/SEU_USUARIO/catalogo-produtos.git
cd catalogo-produtos
```

### 2. Criar Branch
```bash
git checkout -b feature/sua-feature
```

### 3. Configurar o Ambiente

#### Com Docker (recomendado)
```bash
cd projeto-php
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

#### Local
```bash
cd projeto-php
composer install
cp .env.example .env
php artisan key:generate
# Configure o banco de dados no .env
php artisan migrate
```

### 4. Padrões de Código

- Seguir PSR-12 para estilo de código
- Usar Form Requests para validação
- Usar API Resources para formatação de respostas
- Manter controllers enxutos, lógica nos models/services
- Nomear rotas seguindo convenção RESTful
- Documentar endpoints novos no README

### 5. Commits
```bash
# Padrão de mensagens
feat: adiciona endpoint de busca de produtos
fix: corrige validação de preço negativo
docs: atualiza documentação dos endpoints
test: adiciona testes para CategoryController
```

### 6. Testes
```bash
# Com Docker
docker compose exec app php artisan test

# Local
php artisan test
```

### 7. Pull Request
- Abra um PR para a branch `main`
- Descreva as alterações implementadas
- Inclua evidências de testes (prints ou logs)

## Estrutura do Projeto

```
projeto-php/
├── docker/
│   └── nginx/
│       └── default.conf        # Config do Nginx
├── Dockerfile                  # Imagem PHP-FPM
├── docker-compose.yml          # Orquestração dos containers
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/    # Controllers da API
│   │   ├── Requests/           # Form Requests (validação)
│   │   └── Resources/          # API Resources (formatação)
│   └── Models/                 # Eloquent Models
├── database/
│   ├── migrations/             # Migrations do banco
│   └── seeders/                # Seeders para dados de teste
├── routes/
│   └── api.php                 # Rotas da API
└── tests/
    └── Feature/                # Testes de integração
```

## Arquitetura de Referência

Antes de implementar novas features, use a arquitetura documentada em [docs/architecture.md](/home/cleyson-azevedo/Dev/desafio-desenvolvedor-pleno/docs/architecture.md) como referência principal.

Diretrizes obrigatórias:

- controllers apenas orquestram request, service e resource;
- validação sempre em Form Requests;
- regras de negócio e filtros em Services;
- respostas JSON via API Resources;
- rotas REST em `routes/api.php`;
- qualquer desvio dessa estrutura deve ser justificado no PR.

## Critérios de Avaliação

| Critério | Peso |
|----------|------|
| Modelagem do banco de dados | 20% |
| Arquitetura e organização do código | 25% |
| Funcionalidade completa (CRUD, busca, auth) | 20% |
| Segurança e validações | 15% |
| Documentação | 10% |
| Testes automatizados (bônus) | 10% |
