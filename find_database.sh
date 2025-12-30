#!/bin/bash

# Script pour trouver la base de données utilisée par l'application

DB_USER="mysql"
DB_PASS="pw18jkayq10rlx3x"
DB_HOST="adis-database-rjki7t"

echo "🔍 Recherche de la base de données contenant les tables de l'application..."
echo ""

# Trouver la base qui contient la table utilisateurs
BASE_NAME=$(mysql -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" -N -e "
SELECT DISTINCT TABLE_SCHEMA 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_NAME = 'utilisateurs' 
AND TABLE_SCHEMA NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys')
LIMIT 1;
" 2>/dev/null)

if [ -z "$BASE_NAME" ]; then
    echo "❌ Aucune base de données trouvée avec la table 'utilisateurs'"
    echo ""
    echo "📋 Bases de données disponibles :"
    mysql -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" -e "SHOW DATABASES;" 2>/dev/null
    echo ""
    echo "💡 Vous devrez peut-être créer la base de données et exécuter les migrations"
else
    echo "✅ Base de données trouvée : $BASE_NAME"
    echo ""
    echo "📊 Tables dans cette base :"
    mysql -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" -e "USE $BASE_NAME; SHOW TABLES;" 2>/dev/null
    echo ""
    echo "📝 Mettez à jour votre configuration avec :"
    echo "   DB_DATABASE=$BASE_NAME"
    echo ""
    echo "🔧 Dans Dokploy → Environment, modifiez :"
    echo "   DB_DATABASE=$BASE_NAME"
fi

