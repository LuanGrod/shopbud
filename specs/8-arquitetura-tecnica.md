## 8. Arquitetura Técnica

### 8.1 Visão Geral

A aplicação segue uma arquitetura cliente-servidor com comunicação via API REST. O frontend é uma PWA que funciona offline-first, sincronizando dados com o backend quando há conexão disponível.

`┌─────────────────┐         ┌─────────────────┐         ┌─────────────────┐ │                 │  HTTPS  │                 │         │                 │ │   Frontend      │◄───────►│   Backend       │◄───────►│   Banco de      │ │   (Next.js)     │  REST   │   (Laravel)     │         │   Dados         │ │                 │         │                 │         │                 │ └────────┬────────┘         └─────────────────┘         └─────────────────┘          │          │ ┌────────▼────────┐ │                 │ │   IndexedDB     │ │   (Offline)     │ │                 │ └─────────────────┘`

### 8.2 Stack Tecnológica

**Frontend:**

- Next.js (React)
- TypeScript
- Tailwind CSS (estilização)
- IndexedDB via Dexie.js (armazenamento offline)
- Workbox (Service Worker para PWA)

**Backend:**

- PHP 8.x
- Laravel 10.x
- Laravel Sanctum (autenticação API)
- Laravel Socialite (OAuth Google)

**Banco de Dados:**

- MySQL 8.x

**Infraestrutura:**

- Servidor VPS (a definir)
- Nginx
- SSL via Let's Encrypt

### 8.3 Decisão: MySQL vs MongoDB

Optamos por MySQL pelos seguintes motivos:

- As entidades possuem relacionamentos bem definidos (user → template → sector → product)
- Queries de histórico e evolução de gastos são mais simples com SQL
- Laravel Eloquent oferece excelente suporte a MySQL
- Para os campos que precisam de flexibilidade (snapshots), usamos colunas JSON que o MySQL suporta nativamente

### 8.4 Estrutura da API REST

**Autenticação:**

`POST   /api/auth/register POST   /api/auth/login POST   /api/auth/logout POST   /api/auth/google POST   /api/auth/forgot-password POST   /api/auth/reset-password`

**Templates:**

`GET    /api/templates POST   /api/templates GET    /api/templates/{id} PUT    /api/templates/{id} DELETE /api/templates/{id} POST   /api/templates/{id}/share POST   /api/templates/import`

**Setores:**

`GET    /api/templates/{templateId}/sectors POST   /api/templates/{templateId}/sectors PUT    /api/sectors/{id} DELETE /api/sectors/{id} PUT    /api/templates/{templateId}/sectors/reorder`

**Produtos:**

`GET    /api/sectors/{sectorId}/products POST   /api/sectors/{sectorId}/products PUT    /api/products/{id} DELETE /api/products/{id} PUT    /api/sectors/{sectorId}/products/reorder`

**Sessão de Compra:**

`POST   /api/shopping/start GET    /api/shopping/current POST   /api/shopping/items PUT    /api/shopping/items/{id} DELETE /api/shopping/items/{id} POST   /api/shopping/finish POST   /api/shopping/cancel`

**Histórico:**

`GET    /api/history GET    /api/history/{id} DELETE /api/history/{id} GET    /api/history/stats`

**Sincronização:**

`POST   /api/sync`

### 8.5 Estratégia Offline (PWA)

**Service Worker:**

- Cache dos assets estáticos (HTML, CSS, JS, imagens)
- Cache das requisições de leitura (templates, setores, produtos)
- Interceptação de requisições quando offline

**IndexedDB (via Dexie.js):**

- Replica local das entidades: templates, sectors, products, shopping_session, shopping_items
- Campo `sync_status` em cada registro (synced, pending, conflict)
- Campo `local_updated_at` para controle de conflitos

**Fluxo de Sincronização:**

1. Ao iniciar o app, verifica conexão
2. Se online, busca dados do servidor e atualiza IndexedDB
3. Durante uso offline, todas as operações são salvas no IndexedDB com `sync_status: pending`
4. Ao detectar reconexão, dispara sincronização em background
5. Endpoint `/api/sync` recebe batch de alterações pendentes
6. Servidor processa e retorna conflitos (se houver)
7. Cliente resolve conflitos priorizando dados mais recentes

### 8.6 Autenticação

- Tokens JWT via Laravel Sanctum
- Access token com expiração de 7 dias
- Token armazenado no localStorage (frontend)
- Refresh automático quando próximo da expiração
- OAuth com Google via Laravel Socialite

### 8.7 Estrutura de Pastas (sugestão)

**Frontend (Next.js):**

`src/ ├── app/                  # App router (páginas) ├── components/           # Componentes reutilizáveis ├── hooks/                # Custom hooks ├── services/             # Chamadas à API ├── db/                   # Configuração IndexedDB (Dexie) ├── contexts/             # Contexts (auth, shopping, etc) ├── types/                # TypeScript types └── utils/                # Funções utilitárias`

**Backend (Laravel):**

`app/ ├── Http/ │   ├── Controllers/      # Controllers da API │   ├── Requests/         # Form Requests (validação) │   └── Resources/        # API Resources (transformação) ├── Models/               # Eloquent Models ├── Services/             # Lógica de negócio └── Repositories/         # Acesso a dados (opcional)`
