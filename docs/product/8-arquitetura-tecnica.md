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

- PHP 8.4
- Laravel 13.x
- Laravel Sanctum (autenticação API)

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

`POST   /api/auth/register POST   /api/auth/login POST   /api/auth/logout`

**Templates:**

`GET    /api/templates POST   /api/templates GET    /api/templates/{template} PATCH  /api/templates/{template} DELETE /api/templates/{template} POST   /api/templates/{template}/share POST   /api/templates/import`

`GET /api/templates` retorna apenas `id` e `name`, ordenando por `updated_at desc` por padrão. `GET /api/templates/{template}` retorna a estrutura completa do template, com setores ordenados e produtos dentro de cada setor.

**Setores:**

`GET    /api/templates/{template}/sectors POST   /api/templates/{template}/sectors PATCH  /api/templates/{template}/sectors/{sector} DELETE /api/templates/{template}/sectors/{sector} PUT    /api/templates/{template}/sectors/order`

Novos setores entram no final da ordem atual. O endpoint de ordenação recebe os setores do template, valida pertencimento e salva a ordem normalizada como `1, 2, 3...`.

**Produtos:**

`GET    /api/templates/{template}/sectors/{sector}/products POST   /api/templates/{template}/sectors/{sector}/products PATCH  /api/templates/{template}/sectors/{sector}/products/{product} DELETE /api/templates/{template}/sectors/{sector}/products/{product}`

Produtos não têm ordenação manual; a listagem pode usar ordem de criação.

**Sessão de Compra:**

`POST   /api/shopping-sessions/start GET    /api/shopping-sessions/current POST   /api/shopping-sessions/{session}/finish POST   /api/shopping-sessions/{session}/cancel`

Ao iniciar, o backend cria uma sessão `active` com snapshot e validade de 24 horas. Durante a compra, navegação, itens marcados, produtos avulsos, subtotais e total temporário ficam no frontend. Ao finalizar, o frontend envia a lista final de itens; o backend valida, recalcula os subtotais e total oficial, persiste os Shopping Items, cria o histórico e muda a sessão para `finished`.

Se já existir uma sessão `active` válida, o endpoint de início retorna essa sessão em vez de criar outra. Se existir uma sessão `active` expirada, o backend cancela a expirada antes de criar uma nova.

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

- Replica local das entidades necessárias para continuar uma sessão já iniciada: templates, sectors, products, shopping_session e estado local de shopping_items
- Fila local de operações pendentes, especialmente `finish_session`
- Metadados locais para retry e controle da operação pendente

**Fluxo de Sincronização:**

1. Ao iniciar o app, verifica conexão
2. Se online, busca dados do servidor e atualiza IndexedDB
3. Uma sessão de compra só pode ser iniciada online
4. Durante uma sessão já iniciada, alterações de itens ficam locais no frontend
5. Se o usuário finalizar offline, o frontend salva uma operação pendente `finish_session` com o identificador da sessão e os itens finais
6. Ao detectar reconexão, dispara sincronização em background
7. Endpoint `/api/sync` recebe operações pendentes de alto nível, como `finish_session`
8. Servidor rejeita finalizações de sessões expiradas ou canceladas

### 8.6 Autenticação

- Tokens de API via Laravel Sanctum
- Expiração configurada pelo Sanctum, quando necessária
- Token armazenado no localStorage (frontend)

### 8.7 Estrutura de Pastas (sugestão)

**Frontend (Next.js):**

`src/ ├── app/                  # App router (páginas) ├── components/           # Componentes reutilizáveis ├── hooks/                # Custom hooks ├── services/             # Chamadas à API ├── db/                   # Configuração IndexedDB (Dexie) ├── contexts/             # Contexts (auth, shopping, etc) ├── types/                # TypeScript types └── utils/                # Funções utilitárias`

**Backend (Laravel):**

`app/ ├── Http/ │   ├── Controllers/      # Controllers da API │   ├── Requests/         # Form Requests (validação) │   └── Resources/        # API Resources (transformação) ├── Models/               # Eloquent Models ├── Services/             # Lógica de negócio └── Repositories/         # Acesso a dados (opcional)`
