#!/bin/bash

SONAR_HOST_LOCAL="http://localhost:9999"
SONAR_HOST_CONTAINER="http://sonarqube:9000"

echo "=============================="
echo " Iniciando Análise SonarQube"
echo "=============================="

echo "Subindo containers..."
docker compose up -d

echo "Aguardando SonarQube ficar disponível..."
until curl -s $SONAR_HOST_LOCAL > /dev/null; do
    printf "."
    sleep 5
done
echo "SonarQube disponível!"

echo "Instalando dependências..."
docker compose exec app composer install || exit 1

echo "removendo link simbolico storage"
rm -rf public/storage || exit 1

echo "recriando link simbolico"
docker compose exec app php artisan storage:link || exit 1

echo "Executando PHPUnit com cobertura..."
docker compose exec app vendor/bin/phpunit --coverage-clover=coverage.xml || exit 1

echo "Rodando SonarScanner..."
docker compose exec app sonar-scanner || exit 1

echo "Análise concluída!"
echo "➡ Abra: $SONAR_HOST_LOCAL"
