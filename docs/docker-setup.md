# Documentação - Infraestrutura Docker ShopBud

## Índice
1. [Visão Geral](#visão-geral)
2. [Arquitetura Atual](#arquitetura-atual)
3. [Estrutura de Arquivos](#estrutura-de-arquivos)
4. [Explicação do docker-compose.yml](#explicação-do-docker-composeyml)
5. [Comunicação Entre Serviços](#comunicação-entre-serviços)
6. [Variáveis de Ambiente](#variáveis-de-ambiente)
7. [Comandos Úteis](#comandos-úteis)
8. [Troubleshooting](#troubleshooting)

---

## Visão Geral

Este projeto utiliza uma **arquitetura fullstack containerizada** com Docker Compose:

- **Backend**: Laravel (PHP 8.4) rodando em container
- **Frontend**: Next.js (Node 20) rodando em container
- **Banco**: MySQL 8.0 rodando em container

### Por que Docker para tudo?

✅ **Ambiente consistente** - Mesmo ambiente dev/prod
✅ **Setup com um comando** - `docker compose up`
✅ **Isolamento total** - Cada serviço isolado
✅ **Fácil de compartilhar** - Time tem ambiente idêntico
✅ **Próximo da produção** - Simula ambiente real

---

## Arquitetura Atual

```
┌─────────────────────────────────────────────────────────────────┐
│  Docker Host (Sua Máquina)                                      │
│                                                                 │
│  ┌───────────────────────────────────────────────────────────┐ │
│  │  Rede Docker: shopbud_network                             │ │
│  │                                                           │ │
│  │  ┌──────────────┐      ┌──────────────┐                │ │
│  │  │  Frontend    │      │   Backend    │                │ │
│  │  │  (Next.js)   │─────▶│  (Laravel)   │                │ │
│  │  │  Port: 3000  │      │  Port: 8000   │                │ │
│  │  │              │      │              │                │ │
│  │  │ Chama:       │      │ Conecta em:  │                │ │
│  │  │ localhost    │      │ mysql        │                │ │
│  │  │ :8000       │      │ :3306        │                │ │
│  │  └──────────────┘      └──────┬───────┘                │ │
│  │                                ▼                         │ │
│  │                     ┌──────────────┐                    │ │
│  │                     │    MySQL     │                    │ │
│  │                     │  Port: 3306  │                    │ │
│  │                     └──────────────┘                    │ │
│  │                                                           │ │
│  │  Portas expostas para seu host:                          │ │
│  │  3000 → Frontend (localhost:3000)                       │ │
│  │  8000 → Backend (localhost:8000)                        │ │
│  │  3306 → MySQL (localhost:3306)                          │ │
│  └───────────────────────────────────────────────────────────┘ │
│                                                                 │
│  Volumes (persistem dados):                                     │
│  └── mysql_data → Dados do banco                               │
│  └── backend_vendor → Dependências PHP                         │
│  └── frontend_node_modules → Dependências Node                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Estrutura de Arquivos

```
shopbud/
├── docker-compose.yml          ← Orquestra todos os serviços
├── .env                        ← Variáveis do Docker (senhas, portas)
├── .env.example               ← Template de variáveis
├── .gitignore                 ← Ignora .env
│
├── backend/                   ← Laravel API
│   ├── app/                   ← Código da aplicação
│   ├── routes/                ← Rotas da API
│   ├── config/                ← Configurações
│   ├── database/              ← Migrations e Seeders
│   ├── .env                   ← Config Laravel (DB_HOST=mysql)
│   ├── .env.example          ← Template Laravel
│   ├── Dockerfile            ← Build do container backend
│   ├── .dockerignore         ← Arquivos ignorados no build
│   ├── composer.json         ← Dependências PHP
│   ├── phpunit.xml           ← Config testes
│   └── artisan               ← CLI do Laravel
│
├── frontend/                  ← Next.js App
│   ├── app/                  ← Páginas e componentes
│   ├── components/           ← Componentes React
│   ├── public/               ← Arquivos estáticos
│   ├── .env.local            ← Config Next.js
│   ├── Dockerfile            ← Build do container frontend
│   ├── .dockerignore         ← Arquivos ignorados no build
│   ├── package.json          ← Dependências Node
│   ├── next.config.ts        ← Config Next.js
│   └── tsconfig.json         ← Config TypeScript
│
└── docs/
    └── docker-setup.md       ← Este documento
```

---

## Explicação do docker-compose.yml

### Explicação Detalhada por Serviço

#### **Serviço MySQL**

| Parâmetro | Valor | Explicação |
|-----------|-------|------------|
| `image` | `mysql:8.0` | Imagem oficial MySQL versão 8.0 |
| `container_name` | `shopbud_mysql` | Nome fixo para o container |
| `restart` | `unless-stopped` | Reinicia se travar, exceto se parado manualmente |
| `ports` | `"${MYSQL_PORT:-3306}:3306"` | Host:Container (usa .env ou 3306 default) |
| `MYSQL_DATABASE` | `${MYSQL_DATABASE:-shopbud}` | Cria banco automaticamente |
| `MYSQL_ROOT_PASSWORD` | `${MYSQL_ROOT_PASSWORD}` | Senha root (obrigatório do .env) |
| `MYSQL_USER` | `${MYSQL_USER:-shopbud_user}` | Usuário adicional |
| `MYSQL_PASSWORD` | `${MYSQL_PASSWORD}` | Senha do usuário |
| `volumes` | `mysql_data:/var/lib/mysql` | Persiste dados fora do container |
| `healthcheck` | `mysqladmin ping` | Testa se MySQL está respondendo |

#### **Serviço Backend (Laravel)**

| Parâmetro | Valor | Explicação |
|-----------|-------|------------|
| `build.context` | `./backend` | Pasta com código do Laravel |
| `build.dockerfile` | `Dockerfile` | Arquivo de instruções de build |
| `ports` | `"${BACKEND_PORT:-8000}:8000"` | Expõe API na porta 8000 |
| `volumes` | `./backend:/var/www/html` | **SYNC**: Seu código → container (hot reload) |
| `volumes` | `backend_vendor:/vendor` | **PERSISTE**: vendor não re-instala sempre |
| `DB_HOST` | `mysql` | Usa nome do serviço (não 127.0.0.1!) |
| `depends_on` | `mysql: healthy` | Só inicia quando MySQL está pronto |

**IMPORTANTE**: `DB_HOST=mysql` porque estão na mesma rede Docker. O nome `mysql` é resolvido para o IP interno do container.

#### **Serviço Frontend (Next.js)**

| Parâmetro | Valor | Explicação |
|-----------|-------|------------|
| `build.context` | `./frontend` | Pasta com código Next.js |
| `ports` | `"${FRONTEND_PORT:-3000}:3000"` | Expõe app na porta 3000 |
| `volumes` | `./frontend:/app` | **SYNC**: Seu código → container |
| `volumes` | `frontend_node_modules:/app/node_modules` | **PERSISTE**: node_modules |
| `volumes` | `/app/.next` | **EXCLUI**: .next é rebuildado (tmp) |
| `SHOPBUD_API_URL` | `http://backend:8000` | URL interna que o Next.js usa para chamar a API Laravel |

**Nota**: `/app/.next` sem nome antes dos dois pontos significa volume anônimo descartável. O Next.js rebuild o `.next` sempre.

### Volumes

```yaml
volumes:
  mysql_data:
    driver: local
  backend_vendor:
    driver: local
  frontend_node_modules:
    driver: local
```

- **mysql_data**: Persiste dados do banco. Sobrevive `docker compose down`.
- **backend_vendor**: Persiste `vendor/` do PHP. Evita re-instalar composer.
- **frontend_node_modules**: Persiste `node_modules`. Evita re-instalar npm.

### Rede

```yaml
networks:
  shopbud_network:
    driver: bridge
```

- **shopbud_network**: Rede interna onde os containers se comunicam
- **driver: bridge**: Rede padrão Docker, isolada do host

**Resolução de nomes dentro da rede:**
- `frontend` → `http://backend:8000`
- `backend` → `mysql:3306`
- Não usar `localhost` ou `127.0.0.1` para comunicação interna!

---

## Comunicação Entre Serviços

### Fluxo de Requisição

```
1. Browser → localhost:3000
   └─▶ Container Frontend (Next.js)

2. Frontend → http://localhost:8000/api/...
   └─▶ Seu host (porta mapeada)
   └─▶ Container Backend (Laravel)

3. Backend → mysql:3306
   └─▶ Rede interna Docker
   └─▶ Container MySQL
```

### Importante: localhost vs nome do serviço

**Do navegador (fora do Docker):**
```javascript
fetch('http://localhost:8000/api/users')  // ✓ Usa porta mapeada
fetch('http://backend:8000/api/users')   // ✗ Não resolve
```

**Do frontend (dentro do Docker):**
```javascript
// Server-side (Next.js API routes, getServerSideProps)
fetch('http://backend:8000/api/users')   // ✓ Usa rede interna
fetch('http://localhost:8000/api/users') // ✗ Loopback do container
```

**Do backend (dentro do Docker):**
```php
DB_HOST=mysql  // ✓ Nome do serviço na rede
DB_HOST=localhost // ✗ Não funciona
DB_HOST=127.0.0.1 // ✗ Não funciona
```

---

## Comandos Úteis

### Iniciar e Parar

```bash
# Subir todos os serviços (background)
docker compose up -d

# Subir e ver logs em tempo real
docker compose up

# Ver status dos containers
docker compose ps

# Parar containers (não remove)
docker compose stop

# Iniciar containers parados
docker compose start

# Parar e remover containers
docker compose down

# Parar e remover volumes (APAGA DADOS!)
docker compose down -v
```

### Logs

```bash
# Ver todos os logs
docker compose logs

# Ver logs de um serviço
docker compose logs backend
docker compose logs mysql

# Follow mode (tempo real)
docker compose logs -f backend

# Últimas N linhas
docker compose logs --tail=50 backend
```

### Executar Comandos

```bash
# Entrar no container backend
docker compose exec backend bash

# Rodar migration no backend
docker compose exec backend php artisan migrate

# Entrar no tinker do Laravel
docker compose exec backend php artisan tinker

# Rodar testes
docker compose exec backend php artisan test

# Instalar pacote PHP
docker compose exec backend composer require pacote

# Entrar no container frontend
docker compose exec frontend sh

# Instalar pacote Node
docker compose exec frontend npm install pacote

# Conectar no MySQL
docker compose exec mysql mysql -u shopbud_user -p<senha> shopbud

# Conectar como root
docker compose exec mysql mysql -u root -p<senha>
```

### MySQL Direto

```bash
# Via docker exec
docker exec -it shopbud_mysql mysql -u shopbud_user -p<senha>

# Via docker compose
docker compose exec mysql mysql -u shopbud_user -p<senha>

# Comando SQL direto
docker compose exec mysql mysql -u shopbud_user -p<senha> -e "SHOW TABLES;"

# Backup do banco
docker compose exec mysql mysqldump -u root -p<senha> shopbud > backup.sql

# Restaurar backup
docker compose exec -T mysql mysql -u root -p<senha> shopbud < backup.sql
```

### Build e Rebuild

```bash
# Build ou rebuild de um serviço
docker compose build backend
docker compose build --no-cache frontend

# Up com rebuild
docker compose up -d --build

# Forçar rebuild sem cache
docker compose build --no-cache
docker compose up -d
```

### Volumes

```bash
# Listar volumes
docker volume ls

# Inspecionar volume (ver localização)
docker volume inspect shopbud_mysql_data

# Remover volume (CUIDADO: apaga dados!)
docker volume rm shopbud_mysql_data

# Limpar volumes não usados
docker volume prune
```

### Limpeza

```bash
# Remover containers parados
docker container prune

# Remover imagens não usadas
docker image prune

# Limpar tudo (CUIDADO!)
docker system prune -a --volumes
```

---

## Troubleshooting

### Problema: Backend não conecta ao MySQL

**Erro:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Causa:** `DB_HOST` está como `127.0.0.1` em vez de `mysql`.

**Solução:**
```bash
# Editar backend/.env
DB_HOST=mysql

# Reiniciar backend
docker compose restart backend
```

### Problema: Porta já em uso

**Erro:**
```
bind: address already in use
```

**Solução 1:** Parar serviço que está usando a porta
```bash
# Ver quem está usando
sudo lsof -i :3306
sudo lsof -i :8000
sudo lsof -i :3000
```

**Solução 2:** Mudar porta no `.env`
```bash
# .env
MYSQL_PORT=3307
BACKEND_PORT=8001
FRONTEND_PORT=3001
```

### Problema: Container reiniciando_loop

**Erro:**
```
shopbud_backend   Restarting (1) 5 seconds ago
```

**Diagnóstico:**
```bash
docker compose logs backend
```

**Causas comuns:**
1. Erro no PHP
2. Dependência faltando
3. `.env` com erro

**Solução:**
```bash
# Ver logs e corrigir
docker compose logs --tail=100 backend

# Rebuild
docker compose down
docker compose up -d --build
```

### Problema: Código não atualiza (hot reload)

**Causa:** Volume não montado corretamente.

**Verificar:**
```bash
docker compose exec backend ls -la /var/www/html
# Deve mostrar seus arquivos
```

**Solução:**
```bash
docker compose down
docker compose up -d
```

### Problema: Perda de dados do banco

**Causa:** Usou `docker compose down -v` acidentalmente.

**Solução:**
```bash
# NUNCA usar -v a menos que queira apagar tudo
docker compose down    # ✓ Preserva volumes
docker compose down -v # ✗ Apaga volumes
```

### Problema: Variáveis de ambiente não funcionam

**Verificar:**
```bash
# Ver se .env está sendo carregado
docker compose config

# Deve mostrar as variáveis substituídas
```

**Solução:**
```bash
# Verificar formato do .env (sem aspas, sem espaços ao redor de =)
ERRADO:  MYSQL_PASSWORD = "<senha>"
CERTO:   MYSQL_PASSWORD=<senha>

# Reiniciar
docker compose down
docker compose up -d
```

### Problema: node_modules não persiste

**Sintoma:** `npm install` roda toda vez.

**Verificar:**
```bash
docker volume ls | grep node_modules
```

**Solução:**
```bash
# Garantir que volume nomeado está sendo usado
# No docker-compose.yml:
volumes:
  - frontend_node_modules:/app/node_modules  # ✓ nomeado

# Não usar:
volumes:
  - /app/node_modules  # ✗ anônimo, descarta
```

---

## Acesso Externo

### URLs de Acesso

| Serviço | URL | Uso |
|---------|-----|-----|
| Frontend | http://localhost:3000 | Navegador |
| Backend API | http://localhost:8000 | API calls |
| MySQL | localhost:3306 | Cliente DB |

### Exemplo de Uso

```bash
# Frontend
curl http://localhost:3000

# Backend API
curl http://localhost:8000/api/users
curl -H "Authorization: Bearer TOKEN" http://localhost:8000/api/user

# MySQL
mysql -h 127.0.0.1 -P 3306 -u shopbud_user -p<senha> shopbud
```

---

## Próximos Passos

Serviços que podem ser adicionados:

1. **Redis** - Cache e filas
2. **Nginx** - Proxy reverso e static files
3. **Mailpit** - Teste de emails
4. **Adminer** - UI para banco de dados

Exemplo Redis:
```yaml
redis:
  image: redis:alpine
  container_name: shopbud_redis
  restart: unless-stopped
  ports:
    - "6379:6379"
  networks:
    - shopbud_network
```

---

## Resumo

**Estrutura atual:**
- 3 serviços em Docker: MySQL, Laravel (backend), Next.js (frontend)
- 1 rede interna: shopbud_network
- 3 volumes persistem: mysql_data, backend_vendor, frontend_node_modules
- 3 portas expostas: 3000, 8000, 3306

**Comando único para tudo:**
```bash
docker compose up -d
```

**Comando para parar (preservando dados):**
```bash
docker compose down
```

---

**Versão:** 2.0
**Data:** 2026-05-30
**Atualizado para:** Fullstack Docker (Backend + Frontend + MySQL)
