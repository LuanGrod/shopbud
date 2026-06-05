## 3. Requisitos Funcionais

### 3.1 Autenticação

- **RF-001:** O sistema deve permitir que o usuário crie uma conta com nome, e-mail e senha
- **RF-002:** O sistema deve permitir que o usuário faça login com e-mail e senha
- **RF-003:** O sistema deve permitir que o usuário faça logout

### 3.2 Gerenciamento de Templates

- **RF-004:** O sistema deve permitir que o usuário crie um template informando um nome único por usuário (ex: "Assaí")
- **RF-005:** O sistema deve permitir que o usuário edite o nome de um template
- **RF-006:** O sistema deve permitir que o usuário exclua um template
- **RF-007:** O sistema deve permitir que o usuário visualize a lista de seus templates, ordenada por última atualização por padrão
- **RF-008:** O sistema deve permitir que o usuário visualize um template completo com seus setores ordenados e produtos

### 3.3 Gerenciamento de Setores

- **RF-009:** O sistema deve permitir que o usuário crie setores com nome único dentro de um template (ex: "Limpeza", "Higiene", "Hortifruti")
- **RF-010:** O sistema deve adicionar novos setores ao final da ordem atual do template
- **RF-011:** O sistema deve permitir que o usuário edite o nome de um setor
- **RF-012:** O sistema deve permitir que o usuário exclua um setor
- **RF-013:** O sistema deve permitir que o usuário reordene os setores para refletir a ordem real dos corredores do supermercado

### 3.4 Gerenciamento de Produtos

- **RF-014:** O sistema deve permitir que o usuário adicione produtos com nome único dentro de um setor
- **RF-015:** O sistema deve permitir que o usuário edite o nome de um produto
- **RF-016:** O sistema deve permitir que o usuário exclua um produto de um setor
- **RF-017:** O sistema deve listar produtos dentro de um setor pela ordem de criação

### 3.5 Sessão de Compra

- **RF-018:** O sistema deve permitir que o usuário inicie uma sessão de compra selecionando um template como base
- **RF-019:** O sistema deve criar a sessão de compra no backend com status `active` e snapshot do template
- **RF-020:** O sistema deve exigir conexão com a internet para iniciar uma sessão de compra
- **RF-021:** O sistema deve exibir os setores na ordem definida pelo snapshot da sessão
- **RF-022:** O sistema deve exibir os produtos do setor atual durante a navegação
- **RF-023:** O sistema deve permitir que o usuário navegue para o próximo setor
- **RF-024:** O sistema deve permitir que o usuário navegue para o setor anterior
- **RF-025:** O sistema deve permitir que o usuário navegue diretamente para um setor específico
- **RF-026:** O sistema deve permitir que o usuário marque um produto como "pego", informando preço e quantidade, mantendo essa alteração localmente durante a compra
- **RF-027:** O sistema deve permitir que o usuário desmarque um produto já pego, mantendo essa alteração localmente durante a compra
- **RF-028:** O sistema deve permitir que o usuário edite o preço e quantidade de um produto já marcado, mantendo essa alteração localmente durante a compra
- **RF-029:** O sistema deve permitir que o usuário adicione um produto avulso apenas na compra atual, dentro de um setor existente da sessão
- **RF-030:** O sistema não deve alterar o template original ao adicionar produtos avulsos durante uma sessão de compra
- **RF-031:** O sistema deve exibir o subtotal gasto no setor atual em tempo real, calculado no frontend durante a compra
- **RF-032:** O sistema deve exibir o total gasto na compra em tempo real, calculado no frontend durante a compra
- **RF-033:** O sistema deve permitir que o usuário finalize a sessão de compra enviando ao backend a lista final de itens comprados
- **RF-034:** O backend deve recalcular e persistir os subtotais e total oficial ao finalizar a sessão
- **RF-035:** O sistema deve exibir um resumo ao finalizar a compra contendo: lista de produtos comprados com preços e quantidades, subtotal por setor e total geral
- **RF-036:** O sistema deve cancelar automaticamente sessões `active` com mais de 24 horas
- **RF-037:** Se houver uma sessão `active` válida ao tentar iniciar outra compra, o backend deve retornar a sessão existente em vez de criar uma nova

### 3.6 Histórico de Compras

- **RF-038:** O sistema deve salvar automaticamente cada compra finalizada no histórico
- **RF-039:** O sistema deve permitir que o usuário visualize a lista de compras anteriores
- **RF-040:** O sistema deve permitir que o usuário visualize os detalhes de uma compra específica do histórico
- **RF-041:** O sistema deve permitir que o usuário visualize a evolução de gastos totais ao longo do tempo
- **RF-042:** O sistema deve permitir que o usuário visualize a evolução de gastos por setor ao longo do tempo

### 3.7 Compartilhamento de Templates

- **RF-043:** O sistema deve permitir que o usuário compartilhe um template gerando um código temporário baseado em snapshot
- **RF-044:** O sistema deve permitir que o usuário importe um template a partir de um código de compartilhamento válido

### 3.8 Calculadora de Comparação de Preços

- **RF-045:** O sistema deve permitir que o usuário compare dois produtos informando preço e quantidade/peso/volume de cada um
- **RF-046:** O sistema deve exibir qual produto tem o melhor custo-benefício (menor preço por unidade)

### 3.9 Funcionamento Offline

- **RF-047:** O sistema deve permitir que o usuário continue e finalize localmente uma sessão de compra já iniciada online enquanto estiver offline
- **RF-048:** O sistema deve armazenar localmente a finalização pendente de uma sessão offline
- **RF-049:** O sistema deve sincronizar a operação pendente de finalização quando a conexão for restabelecida
- **RF-050:** O sistema não deve permitir iniciar uma sessão de compra offline
