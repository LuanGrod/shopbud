## 4. Requisitos Não Funcionais

### 4.1 Usabilidade

- **RNF-001:** A interface deve ser simples e intuitiva, priorizando usuários com pouca familiaridade tecnológica
- **RNF-002:** O fluxo de compra deve exigir o mínimo de toques possível para ações frequentes (marcar produto, navegar entre setores)
- **RNF-003:** Os totais (setor e geral) devem estar sempre visíveis durante a sessão de compra
- **RNF-004:** O sistema deve ser responsivo, funcionando bem em dispositivos móveis de diferentes tamanhos de tela

### 4.2 Performance

- **RNF-005:** A interface deve responder a interações do usuário em no máximo 200ms em condições normais
- **RNF-006:** A sincronização de dados offline deve ocorrer em segundo plano sem bloquear o uso do app
- **RNF-007:** O app deve carregar e estar utilizável em no máximo 3 segundos em conexões 3G

### 4.3 Disponibilidade e Offline

- **RNF-008:** O sistema deve funcionar offline durante uma sessão de compra após ela ter sido iniciada online
- **RNF-009:** O sistema deve utilizar IndexedDB para armazenamento local dos dados
- **RNF-010:** O sistema deve sincronizar finalizações pendentes de sessões iniciadas online e rejeitar finalizações de sessões expiradas ou canceladas

### 4.4 Segurança

- **RNF-011:** As senhas devem ser armazenadas utilizando hash seguro (bcrypt ou similar)
- **RNF-012:** A comunicação entre cliente e servidor deve ser feita exclusivamente via HTTPS
- **RNF-013:** A autenticação da API deve utilizar tokens do Laravel Sanctum, com expiração configurada quando necessário
- **RNF-014:** Os dados locais sensíveis devem ser limpos ao fazer logout

### 4.5 Compatibilidade

- **RNF-015:** O sistema deve funcionar como PWA, sendo instalável em dispositivos Android e iOS
- **RNF-016:** O sistema deve ser compatível com as versões mais recentes dos navegadores Chrome, Safari, Firefox e Edge
- **RNF-017:** O sistema deve funcionar em dispositivos com Android 8+ e iOS 12+

### 4.6 Manutenibilidade

- **RNF-018:** O código deve seguir os padrões e convenções do Laravel (backend) e Next.js (frontend)
- **RNF-019:** A API deve seguir os princípios RESTful
- **RNF-020:** O código deve ser versionado utilizando Git
