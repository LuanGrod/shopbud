## 10. Roadmap

### 10.1 Visão Geral das Fases

| Fase   | Descrição                  | Duração Estimada |
| ------ | -------------------------- | ---------------- |
| Fase 1 | Setup e Autenticação       | 1-2 semanas      |
| Fase 2 | Gerenciamento de Templates | 2-3 semanas      |
| Fase 3 | Sessão de Compra           | 2-3 semanas      |
| Fase 4 | Histórico e Evolução       | 1-2 semanas      |
| Fase 5 | Compartilhamento           | 1 semana         |
| Fase 6 | Calculadora de Comparação  | 0.5-1 semana     |
| Fase 7 | PWA e Offline              | 2-3 semanas      |
| Fase 8 | Testes e Ajustes Finais    | 1-2 semanas      |

**Duração total estimada:** 11-17 semanas (considerando desenvolvimento part-time como side project)

---

### 10.2 Detalhamento das Fases

**Fase 1: Setup e Autenticação**

Objetivo: Estrutura base do projeto e sistema de login funcionando.

Backend:

- Setup do projeto Laravel
- Configuração do banco de dados MySQL
- Modelagem e migrations (User)
- Configuração do Laravel Sanctum
- Endpoints de autenticação (register, login, logout, forgot-password)
- Integração OAuth Google com Socialite

Frontend:

- Setup do projeto Next.js com TypeScript
- Configuração do Tailwind CSS
- Estrutura de pastas e organização
- Contexto de autenticação
- Telas: Splash, Login, Cadastro, Recuperar Senha
- Integração com API de autenticação
- Armazenamento do token

Entregável: Usuário consegue criar conta, logar e deslogar.

---

**Fase 2: Gerenciamento de Templates**

Objetivo: CRUD completo de templates, setores e produtos.

Backend:

- Migrations (Template, Sector, Product)
- Models e relacionamentos Eloquent
- Endpoints de templates (CRUD)
- Endpoints de setores (CRUD + reordenação)
- Endpoints de produtos (CRUD + reordenação)
- Validações e Form Requests

Frontend:

- Tela Home (lista de templates)
- Tela Criar/Editar Template
- Tela Editar Setor
- Componentes: Card de Template, Card de Setor, Card de Produto
- Funcionalidade de drag-and-drop para reordenação
- Integração com API

Entregável: Usuário consegue criar templates completos com setores e produtos ordenados.

---

**Fase 3: Sessão de Compra**

Objetivo: Fluxo completo de compra funcionando.

Backend:

- Migrations (ShoppingSession, ShoppingItem)
- Endpoints de sessão (start, current, finish, cancel)
- Endpoints de items (adicionar, editar, remover)
- Lógica de cópia do template para sessão (snapshot)
- Cálculo de subtotais e total

Frontend:

- Tela de seleção de template para compra
- Tela de sessão de compra (setor atual)
- Modal de marcar produto (preço + quantidade)
- Modal de adicionar produto avulso
- Modal de navegação entre setores
- Bottom bar com totais em tempo real
- Navegação entre setores (anterior, próximo, direto)
- Tela de resumo da compra
- Tela de sucesso

Entregável: Usuário consegue realizar uma compra completa, marcando produtos e vendo totais.

---

**Fase 4: Histórico e Evolução**

Objetivo: Salvar e visualizar compras anteriores.

Backend:

- Migration (PurchaseHistory)
- Lógica de salvar compra finalizada no histórico
- Endpoints de histórico (listar, detalhe, excluir)
- Endpoint de estatísticas/evolução (gastos por período, por setor)

Frontend:

- Tela de histórico (lista de compras)
- Tela de detalhes da compra
- Tela de evolução de gastos (gráfico)
- Filtros por período e setor
- Integração com biblioteca de gráficos (Chart.js ou Recharts)

Entregável: Usuário consegue ver histórico completo e acompanhar evolução de gastos.

---

**Fase 5: Compartilhamento**

Objetivo: Exportar e importar templates via código.

Backend:

- Migration (SharedTemplate)
- Endpoint de exportação (gera código + snapshot)
- Endpoint de importação (busca por código + cria cópia)
- Lógica de expiração (24h)
- Job/Scheduler para limpar códigos expirados

Frontend:

- Modal de compartilhar template (exibe código)
- Funcionalidade de copiar código
- Modal de importar template (input código + preview)
- Tratamento de erros (código inválido/expirado)

Entregável: Usuário consegue compartilhar templates com outras pessoas via código.

---

**Fase 6: Calculadora de Comparação**

Objetivo: Ferramenta de comparação de preços funcionando.

Frontend:

- Tela/modal de comparação de preços
- Inputs para dois produtos (preço + quantidade)
- Seletor de unidade de medida
- Lógica de cálculo de preço por unidade
- Exibição do resultado com destaque no melhor

Backend: Não necessário (cálculo feito no frontend).

Entregável: Usuário consegue comparar dois produtos e ver qual tem melhor custo-benefício.

---

**Fase 7: PWA e Offline**

Objetivo: App funciona offline e sincroniza quando online.

Frontend:

- Configuração do manifest.json (PWA)
- Configuração do Service Worker com Workbox
- Setup do IndexedDB com Dexie.js
- Replicação das entidades principais no IndexedDB
- Lógica de detecção online/offline
- Interceptação de requisições offline
- Fila de sincronização (operações pendentes)
- UI de status de conexão/sincronização

Backend:

- Endpoint de sincronização em batch (/api/sync)
- Lógica de resolução de conflitos

Testes:

- Testar fluxo completo de compra offline
- Testar sincronização ao reconectar
- Testar conflitos de dados

Entregável: Usuário consegue usar o app sem internet e dados sincronizam automaticamente.

---

**Fase 8: Testes e Ajustes Finais**

Objetivo: Polimento e preparação para lançamento.

Atividades:

- Testes manuais de todos os fluxos
- Correção de bugs encontrados
- Ajustes de responsividade
- Otimização de performance
- Testes em diferentes dispositivos e navegadores
- Revisão de UX (feedbacks visuais, loading states, mensagens de erro)
- Configuração de ambiente de produção
- Deploy

Entregável: App pronto para uso.

---

### 10.3 Priorização (MoSCoW)

**Must Have (Fases 1-3):**

- Autenticação
- Templates, setores, produtos
- Sessão de compra com totais

**Should Have (Fases 4-5):**

- Histórico de compras
- Evolução de gastos
- Compartilhamento de templates

**Could Have (Fase 6):**

- Calculadora de comparação

**Won't Have (MVP):**

- OCR de etiquetas
- Versão nativa mobile

---

### 10.4 Milestones

| Milestone   | Critério de Conclusão                                                        |
| ----------- | ---------------------------------------------------------------------------- |
| M1: Alpha   | Fases 1-3 concluídas. Fluxo básico funcional (criar template, fazer compra). |
| M2: Beta    | Fases 4-6 concluídas. Funcionalidades completas, apenas online.              |
| M3: Release | Fases 7-8 concluídas. PWA offline funcionando, app polido e testado.         |
