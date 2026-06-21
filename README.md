# ShopBud

Lista de compras inteligente para supermercado com controle de gastos em tempo real.

## Sobre o Projeto

ShopBud é um aplicativo que ajuda você a organizar suas compras de supermercado de forma eficiente. Com templates personalizáveis organizados por setores, controle de gastos em tempo real e funcionamento offline, você nunca mais esquecerá um item ou perderá o controle do orçamento.

### Funcionalidades Principais

- **Templates Personalizáveis**: Crie listas organizadas por setores (Hortifruti, Limpeza, etc.) na ordem real do supermercado
- **Controle de Gastos**: Acompanhe o subtotal por setor e o total da compra em tempo real
- **Sessões de Compra**: Navegue entre setores, marque produtos com preço/quantidade e adicione itens avulsos
- **Histórico**: Visualize compras anteriores e acompanhe a evolução de seus gastos
- **Compartilhamento**: Exporte e importe templates via código
- **Funciona Offline**: Use no supermercado sem conexão e sincronize depois
- **Comparador de Preços**: Compare dois produtos e descubra qual tem melhor custo-benefício

## Stack Tecnológico

- **Backend**: Laravel 11 (PHP 8.4) com Laravel Sanctum
- **Frontend**: Next.js 16 (Node 20) com TypeScript e Tailwind CSS
- **Banco de Dados**: MySQL 8.0
- **Containerização**: Docker Compose

## Pré-requisitos

- Docker
- Docker Compose

## Como Rodar

### 1. Clone o Repositório

```bash
git clone https://github.com/seu-usuario/shopbud.git
cd shopbud
```

### 2. Configure as Variáveis de Ambiente

```bash
cp .env.example .env
```

Edite o arquivo `.env` e defina suas senhas:

```env
MYSQL_ROOT_PASSWORD=sua_senha_root_aqui
MYSQL_PASSWORD=sua_senha_aqui
```

### 3. Suba os Serviços

```bash
docker compose up -d
```

### 4. Execute as Migrations

```bash
docker compose exec backend php artisan migrate
docker compose exec backend php artisan db:seed
```

### 5. Acesse o Projeto

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000
- **MySQL**: localhost:3306

## Comandos Úteis

### Gerenciamento dos Containers

```bash
# Ver status dos containers
docker compose ps

# Ver logs em tempo real
docker compose logs -f

# Ver logs de um serviço específico
docker compose logs backend
docker compose logs frontend
docker compose logs mysql

# Parar containers
docker compose stop

# Reiniciar containers
docker compose restart

# Parar e remover containers (preserva dados)
docker compose down

# Parar e remover tudo incluindo volumes (APAGA DADOS!)
docker compose down -v
```

### Backend (Laravel)

```bash
# Entrar no container backend
docker compose exec backend bash

# Executar migrations
docker compose exec backend php artisan migrate

# Criar uma migration
docker compose exec backend php artisan make:migration nome_da_migration

# Rodar seeders
docker compose exec backend php artisan db:seed

# Entrar no tinker
docker compose exec backend php artisan tinker

# Rodar testes
docker compose exec backend php artisan test

# Instalar pacote PHP
docker compose exec backend composer require pacote/autor

# Limpar cache
docker compose exec backend php artisan cache:clear
docker compose exec backend php artisan config:clear
```

### Frontend (Next.js)

```bash
# Entrar no container frontend
docker compose exec frontend sh

# Instalar dependências
docker compose exec frontend npm install

# Instalar pacote
docker compose exec frontend npm install pacote

# Adicionar pacote TypeScript
docker compose exec frontend npm install -D pacote
```

### MySQL

```bash
# Conectar ao MySQL
docker compose exec mysql mysql -u shopbud_user -p

# Conectar como root
docker compose exec mysql mysql -u root -p

# Executar comando SQL direto
docker compose exec mysql mysql -u shopbud_user -p -e "SHOW TABLES;"

# Backup do banco
docker compose exec mysql mysqldump -u root -p shopbud > backup.sql

# Restaurar backup
docker compose exec -T mysql mysql -u root -p shopbud < backup.sql
```

## Estrutura do Projeto

```
shopbud/
├── backend/                 # Laravel API
│   ├── app/                # Código da aplicação
│   ├── routes/            # Rotas da API
│   ├── database/          # Migrations e Seeders
│   ├── .env               # Config Laravel
│   └── Dockerfile         # Build do container
│
├── frontend/               # Next.js App
│   ├── app/               # Páginas e componentes
│   ├── components/        # Componentes React
│   ├── .env.local        # Config Next.js
│   └── Dockerfile        # Build do container
│
├── docker-compose.yml      # Orquestração dos serviços
├── .env                   # Variáveis do Docker
├── .env.example          # Template de variáveis
└── docs/                  # Documentação adicional
```

## Documentação

- [Docker Setup](docs/docker-setup.md) - Documentação completa da infraestrutura Docker
- [Documentação de produto](docs/product/) - Especificações funcionais e não-funcionais do projeto

## Roadmap

| Fase | Descrição | Status |
|------|-----------|--------|
| Fase 1 | Setup e Autenticação | 🟡 Em andamento |
| Fase 2 | Gerenciamento de Templates | ⚪ Não iniciado |
| Fase 3 | Sessão de Compra | ⚪ Não iniciado |
| Fase 4 | Histórico e Evolução | ⚪ Não iniciado |
| Fase 5 | Compartilhamento | ⚪ Não iniciado |
| Fase 6 | Calculadora de Comparação | ⚪ Não iniciado |
| Fase 7 | PWA e Offline | ⚪ Não iniciado |
| Fase 8 | Testes e Ajustes Finais | ⚪ Não iniciado |

## Contribuindo

Contribuições são bem-vindas! Por favor, abra uma issue ou pull request.

## Licença

MIT
