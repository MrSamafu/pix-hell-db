#!/bin/bash
# Script de déploiement PixHellDB
# À exécuter sur le serveur depuis /var/www/pixhelldb

set -e

PROJECT_DIR="/var/www/pixhelldb"
cd "$PROJECT_DIR"

echo "==> Pull des dernières modifications..."
git pull origin main

echo "==> Installation des dépendances PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Installation des dépendances JS..."
npm ci --prefer-offline

echo "==> Build des assets (production)..."
NODE_ENV=production npm run build

echo "==> Migrations base de données..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

echo "==> Vidage forcé du cache Symfony..."
rm -rf var/cache/prod/*

echo "==> Réchauffage du cache Symfony..."
APP_ENV=prod php bin/console cache:warmup

echo "==> Vérification des assets générés..."
cat public/build/entrypoints.json

echo ""
echo "==> Déploiement terminé !"


