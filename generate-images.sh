#!/bin/bash

echo "=== KLASSCI - Assistant de génération d'images ==="
echo "Ce script génère toutes les images thématiques pour KLASSCI"
echo ""

# Définir le chemin du répertoire
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HTDOCS_DIR="$(dirname "$SCRIPT_DIR")"

# Exécuter le script PHP de génération d'images
php $HTDOCS_DIR/public/scripts/generate-all-images.php

echo ""
echo "Vous pouvez maintenant rafraîchir votre navigateur pour voir les images." 