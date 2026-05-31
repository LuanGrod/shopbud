## 3. Requisitos Funcionais

### 3.1 Autenticação

- **RF-001:** O sistema deve permitir que o usuário crie uma conta com nome, e-mail e senha
- **RF-002:** O sistema deve permitir que o usuário faça login com e-mail e senha
- **RF-003:** O sistema deve permitir que o usuário faça login usando conta Google
- **RF-004:** O sistema deve permitir que o usuário faça logout
- **RF-005:** O sistema deve permitir que o usuário recupere sua senha via e-mail

### 3.2 Gerenciamento de Templates

- **RF-006:** O sistema deve permitir que o usuário crie um template informando um nome (ex: "Assaí - Compra Mensal")
- **RF-007:** O sistema deve permitir que o usuário edite o nome de um template
- **RF-008:** O sistema deve permitir que o usuário exclua um template
- **RF-009:** O sistema deve permitir que o usuário visualize a lista de seus templates

### 3.3 Gerenciamento de Setores

- **RF-010:** O sistema deve permitir que o usuário crie setores dentro de um template (ex: "Limpeza", "Higiene", "Hortifruti")
- **RF-011:** O sistema deve permitir que o usuário edite o nome de um setor
- **RF-012:** O sistema deve permitir que o usuário exclua um setor
- **RF-013:** O sistema deve permitir que o usuário reordene os setores arrastando-os para refletir a ordem real dos corredores do supermercado

### 3.4 Gerenciamento de Produtos

- **RF-014:** O sistema deve permitir que o usuário adicione produtos dentro de um setor informando o nome do produto
- **RF-015:** O sistema deve permitir que o usuário edite o nome de um produto
- **RF-016:** O sistema deve permitir que o usuário exclua um produto de um setor
- **RF-017:** O sistema deve permitir que o usuário reordene produtos dentro de um setor

### 3.5 Sessão de Compra

- **RF-018:** O sistema deve permitir que o usuário inicie uma sessão de compra selecionando um template como base
- **RF-019:** O sistema deve exibir os setores na ordem definida pelo usuário no template
- **RF-020:** O sistema deve exibir os produtos do setor atual durante a navegação
- **RF-021:** O sistema deve permitir que o usuário navegue para o próximo setor
- **RF-022:** O sistema deve permitir que o usuário navegue para o setor anterior
- **RF-023:** O sistema deve permitir que o usuário navegue diretamente para um setor específico
- **RF-024:** O sistema deve permitir que o usuário marque um produto como "pego", informando preço e quantidade
- **RF-025:** O sistema deve permitir que o usuário desmarque um produto já pego
- **RF-026:** O sistema deve permitir que o usuário edite o preço e quantidade de um produto já marcado
- **RF-027:** O sistema deve permitir que o usuário adicione um produto avulso apenas na compra atual
- **RF-028:** O sistema deve permitir que o usuário adicione um produto avulso na compra atual e também no template
- **RF-029:** O sistema deve exibir o subtotal gasto no setor atual em tempo real
- **RF-030:** O sistema deve exibir o total gasto na compra em tempo real
- **RF-031:** O sistema deve permitir que o usuário finalize a sessão de compra
- **RF-032:** O sistema deve exibir um resumo ao finalizar a compra contendo: lista de produtos comprados com preços e quantidades, subtotal por setor e total geral

### 3.6 Histórico de Compras

- **RF-033:** O sistema deve salvar automaticamente cada compra finalizada no histórico
- **RF-034:** O sistema deve permitir que o usuário visualize a lista de compras anteriores
- **RF-035:** O sistema deve permitir que o usuário visualize os detalhes de uma compra específica do histórico
- **RF-036:** O sistema deve permitir que o usuário visualize a evolução de gastos totais ao longo do tempo
- **RF-037:** O sistema deve permitir que o usuário visualize a evolução de gastos por setor ao longo do tempo

### 3.7 Compartilhamento de Templates

- **RF-038:** O sistema deve permitir que o usuário exporte um template como arquivo JSON
- **RF-039:** O sistema deve permitir que o usuário importe um template a partir de um arquivo JSON

### 3.8 Calculadora de Comparação de Preços

- **RF-040:** O sistema deve permitir que o usuário compare dois produtos informando preço e quantidade/peso/volume de cada um
- **RF-041:** O sistema deve exibir qual produto tem o melhor custo-benefício (menor preço por unidade)

### 3.9 Funcionamento Offline

- **RF-042:** O sistema deve permitir que o usuário realize uma sessão de compra completa sem conexão com a internet
- **RF-043:** O sistema deve armazenar os dados localmente enquanto estiver offline
- **RF-044:** O sistema deve sincronizar os dados com o servidor quando a conexão for restabelecida
