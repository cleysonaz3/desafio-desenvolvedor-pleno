#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$ROOT_DIR/projeto-php"
SHUTDOWN_DONE=0

cleanup() {
  if [ "$SHUTDOWN_DONE" -eq 1 ]; then
    return
  fi

  SHUTDOWN_DONE=1

  echo
  echo "Encerrando containers do projeto..."
  docker compose down
}

cd "$APP_DIR"

trap cleanup INT TERM

echo "[1/7] Verificando PHP..."
command -v php >/dev/null 2>&1 || {
  echo "PHP não encontrado no PATH."
  exit 1
}

echo "[2/7] Subindo MySQL no Docker..."
docker compose up -d mysql

echo "[2.1/7] Subindo API e front-end no Docker..."
docker compose up -d app nginx frontend

echo "[3/7] Preparando arquivo .env..."
if [ ! -f .env ]; then
  cp .env.example .env
fi

echo "[4/7] Instalando dependências PHP..."
if [ -f vendor/autoload.php ]; then
  echo "Dependências já instaladas em vendor/. Pulando Composer."
elif [ -f ./composer ]; then
  php ./composer install --no-interaction
elif command -v composer >/dev/null 2>&1; then
  composer install --no-interaction
else
  echo "Composer não encontrado. Instale o Composer ou mantenha o binário local em projeto-php/composer."
  exit 1
fi

echo "[5/7] Gerando APP_KEY se necessário..."
if ! grep -q '^APP_KEY=base64:' .env; then
  docker compose exec -T app php artisan optimize:clear
  docker compose exec -T app php artisan key:generate --force
fi

echo "[6/7] Rodando migrations..."
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan migrate --force

echo "[7/7] Garantindo dados de demonstração..."
docker compose exec -T app php artisan demo:seed-api

echo
echo "Ambiente pronto."
echo "API:       http://127.0.0.1:8000"
echo "Status:    http://127.0.0.1:8000/"
echo "Docs:      http://127.0.0.1:8000/docs"
echo "Front-end: http://127.0.0.1:8080"
echo
echo "Pressione Ctrl+C para encerrar o ambiente."
echo

docker compose logs -f app nginx frontend mysql
