#!/bin/bash

echo "🔍 Vérification des prérequis pour KayArt..."
echo ""

# Vérifier PHP
echo "📌 PHP Version:"
php -v | head -n 1

# Vérifier les extensions PHP requises
echo ""
echo "📌 Extensions PHP requises:"
REQUIRED_EXTENSIONS=("gd" "intl" "mbstring" "mysqli" "curl" "zip" "fileinfo" "json" "xml" "dom")

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if php -m | grep -q "^$ext$"; then
        echo "  ✅ $ext"
    else
        echo "  ❌ $ext (MANQUANT!)"
        MISSING=true
    fi
done

# Vérifier Composer
echo ""
echo "📌 Composer:"
if command -v composer &> /dev/null; then
    composer --version | head -n 1
    echo "  ✅ Installé"
else
    echo "  ❌ Composer non trouvé!"
    MISSING=true
fi

# Vérifier Node.js
echo ""
echo "📌 Node.js:"
if command -v node &> /dev/null; then
    node --version
    echo "  ✅ Installé"
else
    echo "  ❌ Node.js non trouvé!"
    MISSING=true
fi

# Vérifier NPM
echo ""
echo "📌 NPM:"
if command -v npm &> /dev/null; then
    npm --version
    echo "  ✅ Installé"
else
    echo "  ❌ NPM non trouvé!"
    MISSING=true
fi

# Vérifier MySQL
echo ""
echo "📌 MySQL/MariaDB:"
if command -v mysql &> /dev/null; then
    mysql --version | head -n 1
    echo "  ✅ Installé"
else
    echo "  ❌ MySQL non trouvé!"
    MISSING=true
fi

# Vérifier les permissions
echo ""
echo "📌 Permissions des dossiers:"
WRITABLE_DIRS=("writable" "public/uploads" "public/writable")

for dir in "${WRITABLE_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        PERMS=$(stat -c "%a" "$dir" 2>/dev/null || stat -f "%Lp" "$dir" 2>/dev/null)
        echo "  📁 $dir: $PERMS"
    else
        echo "  📁 $dir: ❌ N'existe pas"
    fi
done

echo ""
if [ "$MISSING" = true ]; then
    echo "❌ Certains prérequis sont manquants!"
    exit 1
else
    echo "✅ Tous les prérequis sont satisfaits!"
    exit 0
fi
