# 🥟 Pastelaria API

Uma API completa para gerenciamento de pedidos de uma pastelaria, desenvolvida em Laravel com arquitetura limpa e boas práticas.

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Funcionalidades](#funcionalidades)
- [Tecnologias e Versões](#tecnologias-e-versões)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [SonarQube](#sonarqube)
- [Uso da API](#uso-da-api)
- [Testes](#testes)
- [Documentação](#documentação)
- [Deploy](#deploy)

## 🎯 Visão Geral

Esta API permite o gerenciamento completo de uma pastelaria, incluindo cadastro de produtos (pastéis), clientes, pedidos e itens de pedido. A aplicação segue os princípios de Clean Architecture e possui cobertura completa de testes.

## ✨ Funcionalidades

### 🛍️ Gestão de Produtos (Pastéis)

- CRUD completo de produtos
- Categorização (salgado, doce, especial)
- Controle de estoque e disponibilidade
- Upload de fotos dos pastéis

### 👥 Gestão de Clientes

- Cadastro de clientes com endereço completo
- Histórico de pedidos
- Dados de contato

### 📦 Gestão de Pedidos

- Criação de pedidos com múltiplos itens
- Fluxo de status: `pending` → `approved` → `in_preparation` → `ready` → `delivered`
- Cálculo automático de valores
- Soft delete para manter histórico

### 🔄 Funcionalidades Avançadas

- Validações robustas com mensagens personalizadas
- Documentação Swagger/OpenAPI
- Testes unitários e de integração
- Logs e tratamento de erros
- Paginação e filtros

## 🛠 Tecnologias e Versões

### Backend

- **PHP 8.2.29**
- **Laravel 10.x**
- **MySQL 8.0+**
- **SQLite** (para testes)

### Ferramentas de Desenvolvimento

- **PHPUnit 11.5** - Testes unitários
- **Swagger/OpenAPI 3.0** - Documentação
- **Docker & Docker Compose** - Containerização
- **Composer** - Gerenciamento de dependências

### Bibliotecas Principais

- `laravel/sanctum` - Autenticação API
- `laravel/tinker` - Console interativo
- `mockery/mockery` - Mocks para testes
- `doctrine/dbal` - Manipulação de banco

## 📁 Estrutura do Projeto

text
app/
├── Http/
│ ├── Controllers/ # Controladores da API
│ ├── Requests/ # Validações de request
│ └── Middleware/ # Middlewares customizados
├── Models/ # Entidades do sistema
├── Repositories/ # Camada de acesso a dados
│ └── Contracts/ # Interfaces dos repositórios
├── Providers/ # Service providers
└── Exceptions/ # Handlers de exceção

database/
├── factories/ # Factories para testes
├── migrations/ # Migrations do banco
└── seeders/ # Seeders para dados iniciais

tests/
├── Unit/ # Testes unitários
│ ├── Models/ # Testes de models
│ ├── Repositories/ # Testes de repositórios
│ └── Requests/ # Testes de validação
└── Feature/ # Testes de integração

config/ # Configurações da aplicação
routes/ # Rotas da API
public/ # Arquivos públicos
storage/ # Arquivos de storage

## 🚀 Instalação

Pré-requisitos
PHP 8.2+

Composer

MySQL 8.0+

Git

Passo a Passo
Clone o repositório

bash
git clone https://github.com/seu-usuario/pastelaria-api.git
cd pastelaria-api
Instale as dependências

bash
composer install
Configure o ambiente

bash
cp .env.example .env
php artisan key:generate
Configure o banco de dados
Edite o arquivo .env:

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pastelaria
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

APP_TIMEZONE=America/Sao_Paulo
Execute as migrations

bash
php artisan migrate
Popule o banco (opcional)

bash
php artisan db:seed

## ⚙️ Configuração

Timezone
A aplicação está configurada para o fuso horário de São Paulo:

php
'timezone' => 'America/Sao_Paulo'
Configurações Importantes
App Config (config/app.php):

php
'name' => 'Pastelaria API',
'env' => env('APP_ENV', 'production'),
'debug' => env('APP_DEBUG', false),
'timezone' => 'America/Sao_Paulo',
'locale' => 'pt_BR',
Database Config:

Soft Deletes em todas as entidades

Chaves estrangeiras com cascade

Índices otimizados para performance

Sonarqube

Dentro do diretório principal do projeto ./pastel360-api rode ./sonar.sh

Logo após, acesse em http://localhost:9999 com usuário admin e senha Pastel360@2025.

Lá verá todas as méticas de cobertura de código.

## 📡 Uso da API

### Endpoints Principais

#### 🛍️ Produtos

http
GET /api/products # Listar produtos
POST /api/products # Criar produto
GET /api/products/{id} # Buscar produto
PUT /api/products/{id} # Atualizar produto
DELETE /api/products/{id} # Excluir produto
Exemplo de criação:

json
{
"name": "Pastel de Carne",
"description": "Pastel de carne moída com temperos especiais",
"price": 8.50,
"photo": "pastel-carne.jpg",
"stock": 50,
"sku": "PASTEL-CARNE-001",
"enable": true,
"category": "salgado"
}

#### 👥 Clientes

http
GET /api/customers # Listar clientes
POST /api/customers # Criar cliente
GET /api/customers/{id} # Buscar cliente
PUT /api/customers/{id} # Atualizar cliente
DELETE /api/customers/{id} # Excluir cliente

#### 📦 Pedidos

http
GET /api/orders # Listar pedidos
POST /api/orders # Criar pedido
GET /api/orders/{id} # Buscar pedido
PUT /api/orders/{id} # Atualizar pedido
DELETE /api/orders/{id} # Excluir pedido
Exemplo de criação de pedido:

json
{
"customer_id": 1,
"status": "pending",
"notes": "Sem cebola no pastel de carne",
"items": [
{
"product_id": 1,
"quantity": 2,
"unit_value": 8.50
},
{
"product_id": 2,
"quantity": 1,
"unit_value": 7.50
}
]
}
Status dos Pedidos
pending - Aguardando aprovação

approved - Pedido aprovado

in_preparation - Em preparação

ready - Pronto para entrega

delivered - Entregue

canceled - Cancelado

🧪 Testes
Executando os Testes
Todos os testes:

bash
php artisan test
Testes específicos:

bash

# Testes de models

php artisan test --filter=OrderModelTest

# Testes de repositórios

php artisan test --filter=OrderRepositoryTest

# Testes de validação

php artisan test --filter=OrderRequestTest

# Com cobertura de código

php artisan test --coverage --min=80
Estrutura de Testes
Testes Unitários:

OrderModelTest - Testes da entidade Order

OrderItemModelTest - Testes da entidade OrderItem

OrderRepositoryTest - Testes do repositório

OrderRequestTest - Testes de validação

Cobertura Atual:

✅ Models: 100%

✅ Repositories: 100%

✅ Requests: 100%

✅ Relacionamentos: 100%

📚 Documentação
Documentação da API
A API possui documentação Swagger/OpenAPI disponível em:

bash

# Gerar documentação

php artisan l5-swagger:generate

# Acessar documentação

http://localhost:8000/api/documentation
Exemplos de Uso
Criar um pedido:

bash
curl -X POST "http://localhost:8000/api/orders" \
 -H "Content-Type: application/json" \
 -d '{
"customer_id": 1,
"status": "pending",
"items": [
{
"product_id": 1,
"quantity": 2,
"unit_value": 8.50
}
]
}'
Atualizar status do pedido:

bash
curl -X PUT "http://localhost:8000/api/orders/1" \
 -H "Content-Type: application/json" \
 -d '{
"status": "in_preparation"
}'
🐳 Deploy com Docker
Docker Compose
yaml
version: '3.8'
services:
app:
build:
context: .
dockerfile: Dockerfile
container_name: pastelaria-app
restart: unless-stopped
working_dir: /var/www/html
volumes: - .:/var/www/html
environment: - APP_ENV=production - APP_DEBUG=false - APP_TIMEZONE=America/Sao_Paulo

nginx:
image: nginx:alpine
container_name: pastelaria-nginx
restart: unless-stopped
ports: - "8000:80"
volumes: - .:/var/www/html - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf

db:
image: mysql:8.0
container_name: pastelaria-db
restart: unless-stopped
environment:
MYSQL_DATABASE: pastelaria
MYSQL_ROOT_PASSWORD: secret
volumes: - dbdata:/var/lib/mysql

volumes:
dbdata:
Variáveis de Ambiente de Produção
env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sua-pastelaria.com

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=pastelaria
DB_USERNAME=root
DB_PASSWORD=secret

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
🤝 Contribuição
Fork o projeto

Crie uma branch para sua feature (git checkout -b feature/AmazingFeature)

Commit suas mudanças (git commit -m 'Add some AmazingFeature')

Push para a branch (git push origin feature/AmazingFeature)

Abra um Pull Request

Padrões de Código
Seguir PSR-12

Escrever testes para novas funcionalidades

Manter cobertura de código acima de 80%

Documentar endpoints novos no Swagger

📄 Licença
Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

🆘 Suporte
Em caso de problemas:

Verifique a documentação da API

Consulte os logs em storage/logs/

Abra uma issue no GitHub

Desenvolvido com ❤️ para pastelarias 🥟

"Todo mundo merece um pastel quentinho!"
