#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$ROOT_DIR/projeto-php"

cd "$APP_DIR"

echo "[1/7] Verificando PHP..."
command -v php >/dev/null 2>&1 || {
  echo "PHP não encontrado no PATH."
  exit 1
}

echo "[2/7] Subindo MySQL no Docker..."
docker compose up -d mysql

echo "[3/7] Preparando arquivo .env..."
if [ ! -f .env ]; then
  cp .env.example .env
fi

echo "[4/7] Instalando dependências PHP..."
if [ -f ./composer ]; then
  php ./composer install --no-interaction
elif command -v composer >/dev/null 2>&1; then
  composer install --no-interaction
else
  echo "Composer não encontrado. Instale o Composer ou mantenha o binário local em projeto-php/composer."
  exit 1
fi

echo "[5/7] Gerando APP_KEY se necessário..."
if ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate --force
fi

echo "[6/7] Rodando migrations..."
php artisan migrate --force

echo "[7/7] Garantindo dados de demonstração..."
php artisan demo:seed-api

echo
echo "Aplicação pronta."
echo "Status: http://127.0.0.1:8000/"
echo "Docs:   http://127.0.0.1:8000/docs"
echo

exec php artisan serve --host=127.0.0.1 --port=8000
