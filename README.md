# 🥟 PASTEL 360º API

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
- Fluxo de status: `pending` → `approved` → `delivered` → `canceled`
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
- **Laravel 12.x**
- **MySQL 8.0+**
- **SQLite** (para testes)
- **Sonarqube**
- **Xdebug**
- **Nginx**
- **Docker**

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

```bash
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
```

## 🚀 Instalação

Pré-requisitos
PHP 8.2+

Composer

MySQL 8.0+

Git

Passo a Passo
Clone o repositório

```bash
git clone https:/github.com/joseguilhermeromano/Pastel360.git

cd pastel360-api
```

rode o script bash que criei (chamado sonar.sh):

```bash
./sonar.sh
```

Esse script starta cria as imagens/containers/volumes docker, starta os containers, recria o link simbólico do storage local, limpa os caches do laravel, roda todos os testes com phpunit e roda o sonarqube.

Logo após, acesse em http://localhost:9999 com usuário admin e senha Pastel360@2025.

Lá verá todas as méticas de cobertura de código.

Instale as dependências

```bash
docker exec -it app bash
#Aí dentro do container em /var/www/html execute
composer install
```

Configure o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Configure o banco de dados

Edite o arquivo .env:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pastelaria
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

APP_TIMEZONE=America/Sao_Paulo
```

Execute as migrations

```bash
php artisan migrate
```

Popule o banco (opcional)

```bash
php artisan db:seed
```

Configure o mailable.io no seu .env:

```bash
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=6594541d37e2be
MAIL_PASSWORD=335f6bc3ab59eb
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@seudominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## ⚙️ Configuração

Timezone
A aplicação está configurada para o fuso horário de São Paulo:

```bash
'timezone' => 'America/Sao_Paulo'
Configurações Importantes
```

App Config (config/app.php):

```bash
'name' => 'PASTEL 360º API',
'env' => env('APP_ENV', 'production'),
'debug' => env('APP_DEBUG', false),
'timezone' => 'America/Sao_Paulo',
'locale' => 'pt_BR',
```

Database Config:

Soft Deletes em todas as entidades

Chaves estrangeiras com cascade

Índices otimizados para performance

## 📡 Uso da API

### Endpoints Principais

#### 🛍️ Produtos

```bash
GET /api/products # Listar produtos
POST /api/products # Criar produto
GET /api/products/{id} # Buscar produto
PUT /api/products/{id} # Atualizar produto
DELETE /api/products/{id} # Excluir produto
```

Exemplo de criação:

```bash
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
```

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

```bash
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

```

Status dos Pedidos

pending - Aguardando aprovação

approved - Pedido aprovado

delivered - Entregue

canceled - Cancelado

🧪 Testes

Executando os Testes

Todos os testes:

```bash
php artisan test
#ou
./vendor/bin/phpunit
```

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

# 📚 Documentação da API

## Gerar documentação

Acesse o container:

docker exec -it app bash

php artisan l5-swagger:generate

## Acessar documentação

http://localhost/api/documentation

Exemplos de Uso

Criar um pedido:

```bash
curl -X POST "http://localhost/api/orders" \
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

```

Atualizar status do pedido:

```bash
curl -X PUT "http://localhost/api/orders/1" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "in_preparation"
  }'
```

# 🤝 Contribuição

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

# 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

# 🆘 Suporte

Em caso de problemas:

Verifique a documentação da API

Abra uma issue no GitHub

Desenvolvido com ❤️ para pastelarias 🥟

"Todo mundo merece um pastel quentinho!"
