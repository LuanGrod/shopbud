# Documentação - Setup Docker do Projeto ShopBud

## Índice
1. [Visão Geral](#visão-geral)
2. [Arquitetura Atual](#arquitetura-atual)
3. [Explicação do docker-compose.yml](#explicação-do-docker-composeyml)
4. [Comparação: Local vs Docker](#comparação-local-vs-docker)
5. [Como Migrar para Laravel Dockerizado](#como-migrar-para-laravel-dockerizado)
6. [Arquitetura Fullstack (Laravel API + Next.js Frontend)](#arquitetura-fullstack-laravel-api--nextjs-frontend)
7. [Comandos Úteis](#comandos-úteis)
8. [Troubleshooting](#troubleshooting)

---

## Visão Geral

Este projeto utiliza uma **arquitetura híbrida**:
- **Laravel**: Rodando localmente na máquina (PHP instalado no sistema)
- **MySQL**: Rodando em container Docker

### Por que essa escolha?

Esta configuração é ideal para **aprendizado e desenvolvimento local** porque:

✅ **Performance superior** - PHP roda direto na máquina sem overhead de virtualização
✅ **Debugging mais simples** - Logs e Xdebug funcionam diretamente
✅ **Comandos mais simples** - `php artisan` em vez de `docker compose exec app php artisan`
✅ **Editor de código mais rápido** - Auto-complete e file watchers sem delay
✅ **Isolamento do banco de dados** - MySQL containerizado evita conflitos de porta e versões

---

## Arquitetura Atual

```
┌─────────────────────────────────────────────────────┐
│  SUA MÁQUINA (localhost)                            │
│                                                     │
│  ┌──────────────────────┐                          │
│  │  Laravel (PHP 8.x)   │                          │
│  │  - Roda localmente   │                          │
│  │  - Composer local    │                          │
│  │  - Artisan local     │                          │
│  └──────────┬───────────┘                          │
│             │                                       │
│             │ Conecta via 127.0.0.1:3306           │
│             ↓                                       │
│  ┌─────────────────────────────────────┐           │
│  │  PORTA 3306 (mapeada)               │           │
│  └─────────────────────────────────────┘           │
│             ↓                                       │
│  ┌─────────────────────────────────────┐           │
│  │  Docker Container                   │           │
│  │  ┌─────────────────────────────┐    │           │
│  │  │  MySQL 8.0                  │    │           │
│  │  │  - Porta interna: 3306      │    │           │
│  │  │  - Database: shopbud        │    │           │
│  │  │  - User: root/senha123      │    │           │
│  │  └─────────────────────────────┘    │           │
│  │                                     │           │
│  │  Volume: mysql_data                 │           │
│  │  (dados persistem aqui)             │           │
│  └─────────────────────────────────────┘           │
└─────────────────────────────────────────────────────┘
```

### Configuração do Laravel (.env)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1      # localhost (acessa via porta mapeada)
DB_PORT=3306           # porta mapeada do Docker
DB_DATABASE=shopbud    # nome do banco criado automaticamente
DB_USERNAME=root       # usuário configurado no docker-compose
DB_PASSWORD=senha123   # senha configurada no docker-compose
```

---

## Explicação do docker-compose.yml

### Estrutura Completa

```yaml
services:
  mysql:
    image: mysql:8.0
    container_name: shopbud_mysql
    restart: unless-stopped
    ports:
      - "3306:3306"
    environment:
      MYSQL_DATABASE: shopbud
      MYSQL_ROOT_PASSWORD: senha123
      MYSQL_USER: shopbud_user
      MYSQL_PASSWORD: senha123
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - shopbud_network
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

networks:
  shopbud_network:
    driver: bridge

volumes:
  mysql_data:
    driver: local
```

### Explicação Detalhada de Cada Parâmetro

#### **services:**
Define a lista de containers que serão executados.

#### **mysql:**
Nome do serviço. Você escolhe esse nome (poderia ser "database", "db", etc).

#### **image: mysql:8.0**
- Imagem oficial do MySQL versão 8.0 do Docker Hub
- É como baixar o MySQL pré-configurado e pronto para uso
- Outras versões: `mysql:5.7`, `mysql:latest`, etc.

#### **container_name: shopbud_mysql**
- Nome que aparece ao rodar `docker ps`
- Sem isso, Docker gera nome aleatório como "shopbud_mysql_1"
- Útil para identificar facilmente o container

#### **restart: unless-stopped**
Define política de reinicialização do container:
- `unless-stopped`: reinicia automaticamente, exceto se você parar manualmente
- `always`: sempre reinicia (mesmo se parar manualmente)
- `on-failure`: só reinicia se der erro
- `no`: nunca reinicia

#### **ports: "3306:3306"**
Mapeia portas entre sua máquina e o container.

Formato: `"PORTA_HOST:PORTA_CONTAINER"`
- Primeira 3306: porta no seu computador (localhost)
- Segunda 3306: porta dentro do container

Exemplo: `"3307:3306"` significa:
- Acessar em `localhost:3307` do seu PC
- Mas dentro do container continua sendo porta 3306

#### **environment:**
Variáveis de ambiente que o MySQL lê ao iniciar:

- `MYSQL_DATABASE: shopbud`
  Cria automaticamente um banco chamado "shopbud"

- `MYSQL_ROOT_PASSWORD: senha123`
  Define a senha do usuário root

- `MYSQL_USER: shopbud_user`
  Cria um usuário adicional (além do root)

- `MYSQL_PASSWORD: senha123`
  Senha do usuário adicional

> **Importante**: Cada imagem Docker tem suas próprias variáveis. Essas são específicas do MySQL.

#### **volumes: mysql_data:/var/lib/mysql**
Persiste os dados fora do container.

Formato: `VOLUME_EXTERNO:PASTA_NO_CONTAINER`

**Por quê?**
- Containers são temporários (deletar = perder dados)
- Volumes salvam dados no seu PC
- Mesmo deletando o container, os dados ficam em `mysql_data`

`/var/lib/mysql` é onde o MySQL guarda os arquivos do banco internamente.

**Analogia**: É como um HD externo para o container.

#### **networks: shopbud_network**
Rede virtual que conecta containers.

- Containers na mesma rede podem se comunicar
- Se adicionar Laravel aqui, ele acessa MySQL pelo nome "mysql" em vez de IP
- Exemplo: `DB_HOST=mysql` (em vez de `127.0.0.1`)

> **Nota**: No setup atual (Laravel local), essa rede NÃO é usada.

#### **healthcheck:**
Testa automaticamente se o container está funcionando.

```yaml
test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
```
- Comando executado para verificar saúde
- `mysqladmin ping` pergunta ao MySQL "você está vivo?"

```yaml
interval: 10s
```
- Executa o teste a cada 10 segundos

```yaml
timeout: 5s
```
- Se não responder em 5 segundos, considera falha

```yaml
retries: 5
```
- Tenta 5 vezes antes de marcar como "unhealthy"

**Como funciona:**
1. Docker executa o comando automaticamente a cada 10 segundos
2. Você vê o status com: `docker compose ps`
3. Status possíveis: `starting`, `healthy`, `unhealthy`

**Ver detalhes do healthcheck:**
```bash
docker inspect shopbud_mysql --format='{{json .State.Health}}' | python3 -m json.tool
```

#### **networks: (seção global)**
```yaml
networks:
  shopbud_network:
    driver: bridge
```
Define a rede usada pelos containers:
- `shopbud_network`: nome da rede
- `driver: bridge`: tipo de rede (padrão, conecta containers)

Outros tipos:
- `host`: usa a rede do computador diretamente
- `none`: sem rede

#### **volumes: (seção global)**
```yaml
volumes:
  mysql_data:
    driver: local
```
Define o volume usado:
- `mysql_data`: nome do volume
- `driver: local`: salva no disco local

**Onde fica fisicamente?**
```bash
docker volume inspect mysql_data
# Geralmente em: /var/lib/docker/volumes/mysql_data/_data
```

---

## Comparação: Local vs Docker

### Tabela Comparativa

| Aspecto | Laravel Local + MySQL Docker | Laravel Docker + MySQL Docker |
|---------|------------------------------|-------------------------------|
| **Performance** | ⭐⭐⭐⭐⭐ Rápido | ⭐⭐⭐⭐ Bom |
| **Setup inicial** | ⭐⭐ Trabalhoso | ⭐⭐⭐⭐⭐ Muito fácil |
| **Consistência entre ambientes** | ⭐⭐ Variável | ⭐⭐⭐⭐⭐ Idêntico |
| **Facilidade de debug** | ⭐⭐⭐⭐⭐ Direto | ⭐⭐⭐ Requer configuração |
| **Isolamento** | ⭐⭐ Compartilha sistema | ⭐⭐⭐⭐⭐ Total |
| **Uso de recursos** | ⭐⭐⭐⭐⭐ Leve | ⭐⭐⭐ Pesado (RAM/CPU) |
| **Comandos CLI** | ⭐⭐⭐⭐⭐ Simples | ⭐⭐⭐ Mais verbosos |
| **File watchers (Vite)** | ⭐⭐⭐⭐⭐ Rápido | ⭐⭐⭐ Pode ter delay |

### Cenário 1: Laravel Local + MySQL Docker (ATUAL)

#### ✅ Vantagens

**Performance**
- PHP roda direto na máquina (sem overhead)
- Acesso direto aos arquivos
- Hot reload instantâneo

**Simplicidade no Debug**
- Xdebug funciona direto no IDE
- Logs aparecem no terminal normal
- Stack traces claros

**Editor de Código**
- Auto-complete funciona perfeitamente
- File watchers rápidos (Vite, Webpack)
- PHPStorm/VSCode indexam mais rápido

**Comandos Diretos**
```bash
php artisan migrate
composer install
php artisan test
npm run dev
```

#### ❌ Desvantagens

**"Funciona na minha máquina" Syndrome**
- Versão do PHP pode diferir entre devs
- Extensões PHP podem estar faltando
- Configurações diferentes

**Setup Manual**
- Instalar PHP, Composer, extensões
- Configurar versões manualmente
- Cada dev precisa repetir o processo

**Versionamento Difícil**
- Projeto A precisa PHP 8.1
- Projeto B precisa PHP 8.3
- Difícil gerenciar múltiplas versões

**Menos Portável**
- Windows/Mac/Linux comportam diferente
- Paths diferentes (`\` vs `/`)
- Case-sensitivity em arquivos

### Cenário 2: Laravel Docker + MySQL Docker

#### ✅ Vantagens

**Consistência Total**
- Mesma versão PHP para todos
- Mesmas extensões
- Mesmo ambiente dev/produção

**Setup Instantâneo**
```bash
git clone projeto
docker compose up
# Pronto! Tudo funcionando
```

**Múltiplos Projetos**
```bash
# Projeto A
cd projeto-a && docker compose up  # PHP 7.4

# Projeto B
cd projeto-b && docker compose up  # PHP 8.3

# Não conflitam!
```

**Isolamento Total**
- Não "suja" o sistema operacional
- Cada projeto 100% isolado
- Fácil limpar tudo: `docker compose down -v`

**Mais Próximo da Produção**
- Se produção usa Docker → dev idêntico
- Reduz bugs de ambiente

#### ❌ Desvantagens

**Performance Menor**
- Overhead de virtualização
- File I/O mais lento (especialmente Mac/Windows)
- Hot reload pode ter delay

**Comandos Verbosos**
```bash
# Local
php artisan migrate

# Docker
docker compose exec app php artisan migrate
```

**Curva de Aprendizado**
- Precisa entender Docker
- Mais uma camada de abstração
- Debug pode ser complexo

**Consome Mais Recursos**
- RAM: ~500MB-1GB a mais
- Disco: Imagens grandes (500MB+)
- CPU: Processos extras

**File Watchers Problemáticos**
- Vite/Webpack podem ter delay em detectar mudanças
- Volumes no Mac/Windows são lentos
- Pode precisar configurações especiais

---

## Como Migrar para Laravel Dockerizado

Quando quiser migrar para ter o Laravel também no Docker, use o **Laravel Sail** (solução oficial).

### Passo 1: Instalar o Sail

```bash
cd backend
composer require laravel/sail --dev
```

### Passo 2: Publicar a configuração do Sail

```bash
php artisan sail:install
```

Ele perguntará quais serviços você quer:
- [x] mysql (já temos, mas o Sail reconfigura)
- [ ] pgsql
- [ ] mariadb
- [ ] redis
- [ ] memcached
- [ ] meilisearch
- [ ] minio
- [ ] mailpit
- [ ] selenium

**Selecione apenas `mysql`** (já que é o que usamos).

### Passo 3: O Sail criará automaticamente

Ele vai criar/modificar:
- `docker-compose.yml` na raiz do `backend`
- Adicionar scripts de atalho

### Passo 4: Atualizar o .env

```env
# Antes (Laravel local)
DB_HOST=127.0.0.1
DB_PORT=3306

# Depois (Laravel no Docker)
DB_HOST=mysql
DB_PORT=3306
```

> **Atenção**: `mysql` é o nome do serviço no docker-compose. Containers na mesma rede se comunicam pelo nome do serviço.

### Passo 5: Subir os containers

```bash
cd backend
./vendor/bin/sail up -d
```

### Passo 6: Criar alias (opcional mas recomendado)

Adicione no `~/.bashrc` ou `~/.zshrc`:

```bash
alias sail='./vendor/bin/sail'
```

Depois:
```bash
source ~/.bashrc  # ou ~/.zshrc
```

### Passo 7: Usar comandos Sail

```bash
# Artisan
sail artisan migrate
sail artisan tinker

# Composer
sail composer install
sail composer require laravel/sanctum

# NPM
sail npm install
sail npm run dev

# Testes
sail artisan test
sail phpunit

# Entrar no container
sail shell

# Ver logs
sail logs
sail logs -f  # follow mode
```

### Estrutura Completa com Sail

```
shopbud/
├── backend/
│   ├── docker-compose.yml  ← Criado pelo Sail
│   ├── vendor/
│   │   └── bin/
│   │       └── sail        ← Script helper
│   ├── .env                ← DB_HOST=mysql
│   └── ...
├── docker-compose.yml      ← Pode remover (Sail usa o do backend)
└── docs/
    └── docker-setup.md     ← Este arquivo
```

### Comparação de Comandos

| Tarefa | Laravel Local | Laravel Sail |
|--------|---------------|--------------|
| Migrations | `php artisan migrate` | `sail artisan migrate` |
| Instalar pacote | `composer require pkg` | `sail composer require pkg` |
| Rodar testes | `php artisan test` | `sail artisan test` |
| Entrar no bash | - | `sail shell` |
| Ver logs MySQL | `docker compose logs mysql` | `sail logs mysql` |
| Parar serviços | `docker compose stop` | `sail stop` |

### docker-compose.yml do Sail (exemplo)

O Sail cria algo assim:

```yaml
services:
  laravel.test:
    build:
      context: ./vendor/laravel/sail/runtimes/8.3
      dockerfile: Dockerfile
    image: sail-8.3/app
    ports:
      - '${APP_PORT:-80}:80'
    environment:
      WWWUSER: '${WWWUSER}'
      LARAVEL_SAIL: 1
    volumes:
      - '.:/var/www/html'
    networks:
      - sail
    depends_on:
      - mysql

  mysql:
    image: 'mysql:8.0'
    ports:
      - '${FORWARD_DB_PORT:-3306}:3306'
    environment:
      MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
      MYSQL_DATABASE: '${DB_DATABASE}'
    volumes:
      - 'sail-mysql:/var/lib/mysql'
    networks:
      - sail
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-p${DB_PASSWORD}"]

networks:
  sail:
    driver: bridge

volumes:
  sail-mysql:
    driver: local
```

**Diferenças chave:**
- Adiciona serviço `laravel.test` (PHP + Nginx)
- Todos os serviços na rede `sail`
- Laravel acessa MySQL via nome do serviço: `DB_HOST=mysql`
- Volumes mapeiam código do host para `/var/www/html`

**⚠️ IMPORTANTE: Laravel Sail vs Arquitetura Fullstack**

O Laravel Sail é focado **apenas no backend Laravel**. Se você planeja ter:
- Backend Laravel (API)
- Frontend Next.js (ou React, Vue, etc.)

Então você tem **duas opções**:

### Opção 1: Sail apenas para backend + Frontend separado
- Usar `backend/docker-compose.yml` (Sail) para Laravel
- Frontend roda local ou tem seu próprio Docker
- Dois comandos separados: `cd backend && sail up` e `cd frontend && npm run dev`

### Opção 2: Docker Compose customizado na raiz (RECOMENDADO)
- Um único `docker-compose.yml` na raiz do projeto
- Orquestra todos os serviços: Laravel + Next.js + MySQL
- Um comando apenas: `docker compose up`

A **Opção 2** é explicada em detalhes na próxima seção.

---

## Arquitetura Fullstack (Laravel API + Next.js Frontend)

### Cenário: Projeto com Backend e Frontend Separados

Se você está construindo uma aplicação com:
- **Backend**: Laravel como API (REST/GraphQL)
- **Frontend**: Next.js, React, Vue, etc.
- **Banco de dados**: MySQL

Você quer um **único `docker-compose.yml` na raiz** que orquestre tudo.

### Estrutura de Pastas

```
shopbud/
├── docker-compose.yml          ← Arquivo principal (orquestra tudo)
├── backend/                    ← Laravel (API)
│   ├── app/
│   ├── routes/
│   ├── .env                    ← DB_HOST=mysql
│   ├── Dockerfile              ← Build customizado do Laravel
│   └── ...
├── frontend/                   ← Next.js
│   ├── app/
│   ├── components/
│   ├── .env.local              ← NEXT_PUBLIC_API_URL=http://localhost:8000
│   ├── Dockerfile              ← Build customizado do Next.js
│   └── package.json
└── docs/
    └── docker-setup.md
```

### docker-compose.yml Fullstack Completo

Crie na raiz (`/home/luan/projects/shopbud/docker-compose.yml`):

```yaml
services:
  # Backend: Laravel API
  backend:
    build:
      context: ./backend
      dockerfile: Dockerfile
    container_name: shopbud_backend
    restart: unless-stopped
    working_dir: /var/www/html
    ports:
      - "8000:8000"
    environment:
      DB_HOST: mysql
      DB_PORT: 3306
      DB_DATABASE: shopbud
      DB_USERNAME: root
      DB_PASSWORD: senha123
    volumes:
      - ./backend:/var/www/html
      - /var/www/html/vendor           # Exclui vendor do volume
      - /var/www/html/node_modules     # Exclui node_modules do volume
    networks:
      - shopbud_network
    depends_on:
      mysql:
        condition: service_healthy
    command: php artisan serve --host=0.0.0.0 --port=8000

  # Frontend: Next.js
  frontend:
    build:
      context: ./frontend
      dockerfile: Dockerfile
    container_name: shopbud_frontend
    restart: unless-stopped
    working_dir: /app
    ports:
      - "3000:3000"
    environment:
      NEXT_PUBLIC_API_URL: http://localhost:8000
      NODE_ENV: development
    volumes:
      - ./frontend:/app
      - /app/node_modules              # Exclui node_modules do volume
      - /app/.next                     # Exclui .next do volume
    networks:
      - shopbud_network
    depends_on:
      - backend
    command: npm run dev

  # Banco de dados: MySQL
  mysql:
    image: mysql:8.0
    container_name: shopbud_mysql
    restart: unless-stopped
    ports:
      - "3306:3306"
    environment:
      MYSQL_DATABASE: shopbud
      MYSQL_ROOT_PASSWORD: senha123
      MYSQL_USER: shopbud_user
      MYSQL_PASSWORD: senha123
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - shopbud_network
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

networks:
  shopbud_network:
    driver: bridge

volumes:
  mysql_data:
    driver: local
```

### Dockerfile do Backend (Laravel)

Crie `backend/Dockerfile`:

```dockerfile
FROM php:8.3-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto
COPY . .

# Instalar dependências do Composer
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expor porta
EXPOSE 8000

# Comando padrão (será sobrescrito pelo docker-compose)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
```

### Dockerfile do Frontend (Next.js)

Crie `frontend/Dockerfile`:

```dockerfile
FROM node:20-alpine

# Definir diretório de trabalho
WORKDIR /app

# Copiar package.json e package-lock.json
COPY package*.json ./

# Instalar dependências
RUN npm install

# Copiar código fonte
COPY . .

# Expor porta
EXPOSE 3000

# Comando padrão (será sobrescrito pelo docker-compose)
CMD ["npm", "run", "dev"]
```

### Como os Serviços se Comunicam

```
┌──────────────────────────────────────────────────────┐
│  Rede Docker: shopbud_network                        │
│                                                      │
│  ┌────────────────┐       ┌────────────────┐       │
│  │   Frontend     │──────▶│    Backend     │       │
│  │   (Next.js)    │       │   (Laravel)    │       │
│  │   Port: 3000   │       │   Port: 8000   │       │
│  │                │       │                │       │
│  │ Chama API via: │       │ Conecta em:    │       │
│  │ backend:8000   │       │ mysql:3306     │       │
│  └────────────────┘       └────────┬───────┘       │
│         ▲                           │               │
│         │                           ▼               │
│         │                  ┌────────────────┐       │
│    Usuário acessa          │     MySQL      │       │
│   localhost:3000           │   Port: 3306   │       │
│                            └────────────────┘       │
│                                                      │
└──────────────────────────────────────────────────────┘

Comunicação INTERNA (dentro da rede Docker):
- Frontend → Backend: http://backend:8000
- Backend → MySQL: mysql:3306

Comunicação EXTERNA (do navegador):
- Usuário → Frontend: http://localhost:3000
- Usuário → Backend (se necessário): http://localhost:8000
```

### Configuração do Backend (.env)

`backend/.env`:

```env
APP_NAME=ShopBud
APP_URL=http://localhost:8000

# Database (usa nome do serviço Docker)
DB_CONNECTION=mysql
DB_HOST=mysql              # Nome do serviço no docker-compose
DB_PORT=3306               # Porta INTERNA do container
DB_DATABASE=shopbud
DB_USERNAME=root
DB_PASSWORD=senha123

# CORS (permitir frontend)
SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

### Configuração do Frontend (.env.local)

`frontend/.env.local`:

```env
# API URL (comunicação interna entre containers)
NEXT_PUBLIC_API_URL=http://localhost:8000

# Se o frontend fizer requests server-side:
API_URL_SERVER=http://backend:8000
```

**Importante:**
- `NEXT_PUBLIC_*` = variáveis expostas ao navegador (usa `localhost`)
- `API_URL_SERVER` = para requests server-side no Next.js (usa nome do serviço `backend`)

### Configuração CORS no Laravel

`backend/config/cors.php`:

```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

### Como Usar

#### 1. Subir todos os serviços

```bash
# Na raiz do projeto
docker compose up -d

# Ver logs de todos os serviços
docker compose logs -f

# Ver logs de um serviço específico
docker compose logs -f backend
docker compose logs -f frontend
```

#### 2. Rodar migrations no backend

```bash
docker compose exec backend php artisan migrate
```

#### 3. Instalar dependências

```bash
# Backend (Laravel)
docker compose exec backend composer install

# Frontend (Next.js)
docker compose exec frontend npm install
```

#### 4. Acessar

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000/api
- **MySQL**: localhost:3306

#### 5. Comandos úteis

```bash
# Entrar no container do backend
docker compose exec backend bash

# Entrar no container do frontend
docker compose exec frontend sh

# Rodar testes no backend
docker compose exec backend php artisan test

# Rebuild após mudanças no Dockerfile
docker compose up -d --build

# Parar tudo
docker compose down

# Parar e limpar volumes (CUIDADO!)
docker compose down -v
```

### Vantagens dessa Arquitetura

✅ **Tudo em um lugar** - Um comando sobe backend + frontend + banco
✅ **Comunicação facilitada** - Serviços se comunicam via nomes (não IPs)
✅ **Consistência** - Todos os devs têm ambiente idêntico
✅ **Isolamento** - Cada serviço no seu container
✅ **Fácil de entender** - Estrutura clara e organizada
✅ **Próximo da produção** - Simula ambiente real

### Desvantagens

❌ **Build inicial lento** - Primeira vez demora (baixa imagens, instala deps)
❌ **Consome mais recursos** - 3 containers rodando
❌ **Hot reload mais lento** - Especialmente no Windows/Mac
❌ **Debugging mais complexo** - Precisa configurar Xdebug via Docker

### Comparação: Sail vs Fullstack Customizado

| Aspecto | Laravel Sail | Docker Compose Fullstack |
|---------|--------------|--------------------------|
| **Escopo** | Apenas backend | Backend + Frontend + DB |
| **Configuração** | Pronta (sail install) | Manual (criar Dockerfiles) |
| **Flexibilidade** | Limitada | Total |
| **Frontend** | Não incluído | Incluído |
| **Complexidade** | Baixa | Média |
| **Aprendizado** | Menos | Mais (entende Docker) |
| **Recomendado para** | APIs puras | Apps fullstack |

### Quando Usar Cada Abordagem

**Use Laravel Sail quando:**
- Projeto é apenas uma API
- Quer algo pronto rapidamente
- Não precisa customizar muito

**Use Docker Compose Fullstack quando:**
- Tem frontend separado (Next.js, React, Vue)
- Precisa orquestrar múltiplos serviços
- Quer controle total da configuração
- Quer aprender Docker profundamente

### Evolução Recomendada

Para aprendizado, sugerimos esta progressão:

1. **Fase 1 (Atual)**: Laravel local + MySQL Docker
   - Foco em aprender Laravel
   - Setup simples

2. **Fase 2**: Backend Docker + MySQL Docker
   - Aprender Docker básico
   - Usar Laravel Sail

3. **Fase 3**: Fullstack Docker
   - Adicionar frontend Next.js
   - Docker Compose customizado
   - Entender comunicação entre containers

4. **Fase 4**: Adicionar mais serviços
   - Redis para cache
   - Nginx como proxy reverso
   - Mailpit para emails
   - MinIO para storage

---

## Comandos Úteis

### Gerenciar Containers

```bash
# Subir os containers (modo detached - background)
docker compose up -d

# Subir e ver logs (modo attached - foreground)
docker compose up

# Parar containers (não remove)
docker compose stop

# Iniciar containers parados
docker compose start

# Reiniciar containers
docker compose restart

# Parar e remover containers
docker compose down

# Parar e remover containers + volumes (CUIDADO: apaga dados!)
docker compose down -v

# Ver containers rodando
docker compose ps

# Ver logs
docker compose logs mysql
docker compose logs -f mysql  # follow mode (tempo real)
docker compose logs --tail=100 mysql  # últimas 100 linhas
```

### Gerenciar Volumes

```bash
# Listar volumes
docker volume ls

# Inspecionar volume (ver onde está fisicamente)
docker volume inspect mysql_data

# Remover volume (CUIDADO: apaga dados!)
docker volume rm mysql_data

# Remover volumes não usados
docker volume prune
```

### Gerenciar Redes

```bash
# Listar redes
docker network ls

# Inspecionar rede
docker network inspect shopbud_network

# Remover rede
docker network rm shopbud_network
```

### Executar Comandos no Container

```bash
# Entrar no bash do MySQL
docker compose exec mysql bash

# Conectar no MySQL via CLI
docker compose exec mysql mysql -u root -psenha123 shopbud

# Executar comando SQL direto
docker compose exec mysql mysql -u root -psenha123 -e "SHOW DATABASES;"

# Ver logs do MySQL
docker compose exec mysql tail -f /var/log/mysql/error.log
```

### Backup e Restore

```bash
# Fazer backup do banco
docker compose exec mysql mysqldump -u root -psenha123 shopbud > backup.sql

# Restaurar backup
docker compose exec -T mysql mysql -u root -psenha123 shopbud < backup.sql

# Backup de todos os bancos
docker compose exec mysql mysqldump -u root -psenha123 --all-databases > backup_all.sql
```

### Healthcheck

```bash
# Ver status de saúde
docker compose ps

# Ver histórico detalhado do healthcheck
docker inspect shopbud_mysql --format='{{json .State.Health}}' | python3 -m json.tool

# Ver apenas o status
docker inspect shopbud_mysql --format='{{.State.Health.Status}}'
```

### Limpeza Geral

```bash
# Remover containers parados
docker container prune

# Remover volumes não usados
docker volume prune

# Remover redes não usadas
docker network prune

# Remover imagens não usadas
docker image prune

# Limpar TUDO (cuidado!)
docker system prune -a --volumes
```

---

## Troubleshooting

### Problema: Porta 3306 já está em uso

**Erro:**
```
Error: failed to bind host port for 0.0.0.0:3306:172.20.0.2:3306/tcp: address already in use
```

**Causa:** Outro MySQL/MariaDB rodando localmente.

**Solução 1: Parar o MySQL local**
```bash
sudo systemctl stop mysql
sudo systemctl disable mysql  # impede iniciar no boot
```

**Solução 2: Usar porta diferente**

Editar `docker-compose.yml`:
```yaml
ports:
  - "3307:3306"  # host:container
```

E atualizar `.env`:
```env
DB_PORT=3307
```

### Problema: Container não fica healthy

**Erro:**
```
shopbud_mysql   Up 2 minutes (unhealthy)
```

**Diagnóstico:**
```bash
# Ver logs do healthcheck
docker inspect shopbud_mysql --format='{{json .State.Health}}' | python3 -m json.tool

# Ver logs do MySQL
docker compose logs mysql
```

**Possíveis causas:**
1. MySQL ainda inicializando (espere 30-60s)
2. Senha incorreta no healthcheck
3. MySQL crashou

**Solução:**
```bash
# Reiniciar container
docker compose restart mysql

# Se persistir, recriar do zero
docker compose down -v
docker compose up -d
```

### Problema: Laravel não conecta ao MySQL

**Erro:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Checklist:**
1. Container está rodando?
   ```bash
   docker compose ps
   # Deve mostrar "Up X minutes (healthy)"
   ```

2. Credenciais corretas no `.env`?
   ```env
   DB_HOST=127.0.0.1  # ou 'mysql' se Laravel no Docker
   DB_PORT=3306
   DB_DATABASE=shopbud
   DB_USERNAME=root
   DB_PASSWORD=senha123
   ```

3. Porta mapeada corretamente?
   ```bash
   docker compose ps
   # Deve mostrar: 0.0.0.0:3306->3306/tcp
   ```

4. MySQL aceitando conexões?
   ```bash
   docker compose exec mysql mysql -u root -psenha123 -e "SELECT 1;"
   ```

**Solução:**
```bash
# Limpar cache do Laravel
php artisan config:clear
php artisan cache:clear

# Testar conexão
php artisan migrate:status
```

### Problema: Volumes não persistem dados

**Sintoma:** Dados somem após `docker compose down`.

**Causa:** Volumes não configurados ou removidos com `-v`.

**Solução:**
1. Verificar se volume existe:
   ```bash
   docker volume ls | grep mysql
   ```

2. Se não existir, recriar:
   ```bash
   docker compose up -d
   ```

3. **Nunca use** `docker compose down -v` a menos que queira APAGAR os dados!

### Problema: Permissões no volume

**Erro:**
```
Permission denied in /var/lib/mysql
```

**Solução:**
```bash
# Verificar permissões do volume
docker volume inspect mysql_data

# Recriar volume com permissões corretas
docker compose down -v  # CUIDADO: apaga dados!
docker compose up -d
```

### Problema: Warning sobre "version is obsolete"

**Warning:**
```
WARN[0000] the attribute `version` is obsolete
```

**Causa:** Docker Compose V2 não precisa mais de `version:`.

**Solução:** Remover linha do `docker-compose.yml`:
```yaml
# Remover esta linha
version: '3.8'

# Começar direto com
services:
  mysql:
    ...
```

### Problema: Queries lentas

**Sintoma:** Banco de dados está lento.

**Diagnóstico:**
```bash
# Ver uso de recursos
docker stats shopbud_mysql

# Ver queries lentas
docker compose exec mysql mysql -u root -psenha123 -e "SHOW FULL PROCESSLIST;"
```

**Soluções:**
1. Aumentar memória do Docker Desktop (se Mac/Windows)
2. Adicionar índices no banco
3. Otimizar queries do Laravel
4. Adicionar cache (Redis)

---

## Recursos Adicionais

### Documentação Oficial

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [MySQL Docker Hub](https://hub.docker.com/_/mysql)
- [Laravel Sail](https://laravel.com/docs/sail)

### Ferramentas Úteis

- **Docker Desktop**: GUI para gerenciar containers (Mac/Windows)
- **Portainer**: Web UI para gerenciar Docker
- **DBeaver**: Cliente universal de banco de dados
- **TablePlus**: Cliente MySQL/PostgreSQL moderno

### Próximos Passos

Após dominar o básico, considere adicionar:

1. **Redis** para cache e filas
   ```yaml
   redis:
     image: redis:alpine
     ports:
       - "6379:6379"
   ```

2. **Mailpit** para testar emails localmente
   ```yaml
   mailpit:
     image: axllent/mailpit
     ports:
       - "8025:8025"  # Web UI
       - "1025:1025"  # SMTP
   ```

3. **Nginx** como proxy reverso
4. **PostgreSQL** se quiser testar outro banco
5. **Elasticsearch** para busca avançada

---

## Conclusão

Este documento serve como referência completa para entender e gerenciar o setup Docker do projeto ShopBud.

**Setup atual:**
- Laravel rodando localmente (performance + simplicidade)
- MySQL rodando no Docker (isolamento + facilidade)
- Melhor dos dois mundos para aprendizado

**Quando migrar para Docker completo:**
- Time crescer (múltiplos desenvolvedores)
- Precisar de consistência total
- Deploy em ambientes containerizados
- Múltiplos projetos com versões diferentes

---

**Dúvidas?** Consulte a seção [Troubleshooting](#troubleshooting) ou a documentação oficial do Docker.

**Autor:** Documentado durante o processo de aprendizado do projeto ShopBud
**Data:** 2026-01-26
**Versão:** 1.0
