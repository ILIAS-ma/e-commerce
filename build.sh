#!/bin/bash
set -e

echo "🚀 Début du build..."

# Vérifier la version PHP
echo "📋 Version PHP:"
php --version

# Installer les dépendances
echo "📦 Installation des dépendances..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Vérifier que Symfony est installé
echo "🔍 Vérification Symfony..."
php bin/console --version

# Nettoyer le cache (optionnel)
echo "🧹 Nettoyage du cache..."
php bin/console cache:clear --env=prod --no-interaction || echo "⚠️ Cache clear échoué, continuant..."

echo "✅ Build terminé avec succès!" 