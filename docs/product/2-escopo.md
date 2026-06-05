## 2. Escopo

### 2.1 Dentro do Escopo (MVP)

- [x]  Cadastro e autenticação de usuários
    - [x]  rotas de cadastro, login e logout
    - [x]  rate limiter (1 p/ login e outro global)
    - [x]  rotas protegidas via sanctum
    - [x]  testes
- [ ]  Criação e gerenciamento de templates de lista por supermercado
- [ ]  Criação e ordenação de setores dentro dos templates
- [ ]  Cadastro de produtos dentro dos setores
- [ ]  Início de uma sessão de compra baseada em um template
- [ ]  Navegação entre setores durante a compra (próximo, anterior, ir para específico)
- [ ]  Marcação de produtos com preço e quantidade durante a compra
- [ ]  Adição de produtos avulsos apenas na compra atual
- [ ]  Exibição do gasto parcial por setor e gasto total em tempo real
- [ ]  Tela de resumo ao finalizar a compra
- [ ]  Calculadora de comparação de preços (preço por unidade/litro/kg)
- [ ]  Continuação e finalização offline de uma sessão iniciada online, com sincronização posterior da finalização
- [ ]  Compartilhamento de templates via código temporário baseado em snapshot
- [ ]  Histórico de compras finalizadas com detalhamento completo (produtos, preços, quantidades, setores, data)
- [ ]  Visualização da evolução de gastos ao longo do tempo (total e por setor)

### 2.2 Fora do Escopo

- Integração com APIs de supermercados para busca de preços
- Sugestões automáticas de produtos baseadas em compras passadas
- Leitura de código de barras
- Notificações e lembretes
- Versão nativa mobile (será PWA)
- Exportação de relatórios para formatos externos (PDF, planilha)
- Modo colaborativo em tempo real
- Integração com assistentes de voz

### 2.3 Futuro (possíveis evoluções)

- Leitura de etiquetas de preço via câmera (OCR) para captura automática de nome e preço do produto
- Recuperação de senha com fluxo completo de "esqueci minha senha", envio de e-mail, token temporário e redefinição segura
- Login com Google
