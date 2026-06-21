## 9. Identidade Visual e Wireframes

### 9.1 Identidade Visual

**Nome:** ShopBud

**Tagline:** "Seu companheiro de compras"

**Tom de voz:** Amigável, direto e encorajador. O app fala com o usuário de forma simples, sem termos técnicos, como um amigo ajudando nas compras.

**Paleta de Cores:**

A paleta abaixo reflete os tokens definidos em `frontend/app/globals.css`.

| Função | Token Tailwind | Cor | Hex | Uso |
| --- | --- | --- | --- | --- |
| Fundo | `bg-background` / `brand-ivory` | Ivory Quente | `#FFF7EC` | Background geral das telas |
| Texto Principal | `text-foreground` / `brand-graphite` | Grafite Profundo | `#232323` | Títulos, textos de corpo e ícones neutros |
| Superfície | `bg-surface` / `brand-surface` | Branco | `#FFFFFF` | Cards, modais e inputs |
| Superfície Quente | `bg-surface-warm` | Ivory Suave | `#FFFBF3` | Áreas internas, painéis leves e estados calmos |
| Borda | `border-border` / `brand-border` | Neutro Quente | `#EADFCE` | Bordas de cards, inputs e divisores |
| Primária | `bg-primary` / `brand-leaf` | Verde Folha | `#2E7D32` | Botões principais, marca, checks e ações positivas |
| Primária sobre fundo | `text-primary-foreground` | Ivory Quente | `#FFF7EC` | Texto e ícones sobre fundo primário |
| Secundária | `bg-secondary` / `brand-sage` | Verde Sálvia | `#E7F0E3` | Badges, estados selecionados suaves e cards marcados |
| Texto Secundário | `text-muted-foreground` / `brand-muted-foreground` | Verde Acinzentado | `#667461` | Labels, placeholders e textos auxiliares |
| Destaque | `bg-accent` / `brand-yellow` | Amarelo Suave | `#FFD85A` | Destaques, categorias e feedbacks leves |
| Texto sobre Destaque | `text-accent-foreground` | Grafite Profundo | `#232323` | Texto e ícones sobre amarelo |
| Erro | `bg-destructive` / `brand-tomato` | Vermelho Tomate | `#E14B3C` | Erros, validações e ações destrutivas |
| Texto sobre Erro | `text-destructive-foreground` | Ivory Quente | `#FFF7EC` | Texto e ícones sobre fundo de erro |
| Neutro | `bg-muted` / `brand-neutral` | Neutro Claro | `#F2F2F2` | Áreas desativadas, fundos neutros e placeholders |
| Foco | `ring-ring` | Verde Folha | `#2E7D32` | Focus ring e estados ativos |

**Tipografia:**

- Fonte de títulos: SF Pro Display (`font-heading` / `font-display`)
- Fonte de corpo: SF Pro Text (`font-sans`)
- Fonte de marca: Baloo 2 (`font-brand` / `font-baloo`)
- Títulos: SF Pro Display Semibold/Bold, 20-24px
- Corpo: SF Pro Text Regular, 16px
- Auxiliar: SF Pro Text Regular/Medium, 14px
- Valores monetários: SF Pro Display Semibold/Bold, para destaque
- Marca e momentos expressivos: Baloo 2 Bold

SF Pro Display está disponível nos pesos `100` a `900`. SF Pro Text está disponível nos pesos `300` a `800`. Baloo 2 está disponível em Regular (`400`) e Bold (`700`).

**Iconografia:**

- Estilo: Outlined, traços arredondados
- Tamanho padrão: 24px
- Biblioteca: Phosphor Icons para React (`@phosphor-icons/react`)
- Peso padrão: `regular`
- Peso de destaque: `bold`, apenas para ações principais, estados selecionados ou momentos de marca

**Bordas e Sombras:**

- Border radius padrão: 12px (botões, cards, inputs)
- Border radius pequeno: 8px (badges, tags)
- Sombra sutil em cards: `0 2px 8px rgba(0,0,0,0.08)`

**Espaçamento:**

- Base: 8px
- Padding de cards: 16px
- Gap entre elementos: 12px
- Margens laterais de tela: 16px

---

### 9.2 Diretrizes Responsivas

O ShopBud é mobile-first. A experiência principal acontece no celular, durante a compra no supermercado, então todos os fluxos devem ser desenhados primeiro para uso vertical, com uma mão e com o mínimo de toques.

O layout mobile é a fonte da verdade. Tablet e desktop não devem introduzir outra navegação, outra densidade de informação ou layouts laterais que mudem o comportamento do app.

**Viewport do app:**

- Em telas pequenas, o app ocupa `100%` da largura disponível.
- Em telas grandes, o app fica centralizado na página com largura fixa de celular.
- A largura recomendada do shell do app em desktop é entre `390px` e `430px`.
- O shell deve manter orientação vertical, com rolagem interna normal da página.
- A área externa ao shell representa a borda fora do app e pode usar Grafite Profundo (`#232323`) ou Branco (`#FFFFFF`) para destacar o conteúdo central.

**Desktop e tablet:**

- Desktop e tablet exibem a mesma experiência mobile centralizada.
- Não usar grids laterais, painéis persistentes ou layout em múltiplas colunas para os fluxos principais.
- Não mover ações fixas para barras laterais no desktop.
- Bottom bars, FABs, headers e botões fixos continuam no mesmo padrão mobile.
- Modais e bottom sheets continuam seguindo o padrão mobile, limitados à largura do shell.

**Densidade e toque:**

- Botões e áreas clicáveis devem ter altura mínima de `44px`, preferencialmente `48px`.
- Cards e listas devem manter leitura vertical, com uma ação principal clara por bloco.
- Conteúdo crítico da sessão de compra, como subtotal e total, deve permanecer sempre visível ou facilmente acessível.
- Evitar interações que dependam de hover; hover pode existir como reforço visual, mas não deve ser necessário para usar o app.

**Padrão de implementação:**

- A raiz visual da aplicação deve funcionar como um app shell centralizado.
- O conteúdo interno usa `bg-background`; a área externa pode usar `bg-brand-graphite` ou `bg-brand-surface`.
- A largura máxima do app shell deve ser aplicada no contêiner de layout, não em cada tela individual.
- Telas específicas podem ter conteúdo mais simples ou mais denso, mas sempre dentro da mesma coluna vertical.

---

### 9.3 Componentes Principais

Esta seção define contratos de uso. Os detalhes finais de espaçamento, sombra e estados visuais devem viver nos componentes do frontend, usando os tokens da identidade visual.

**Botões**

- Uso: ações de confirmação, navegação e comandos diretos.
- Primário: usar `bg-primary` e `text-primary-foreground` para a ação principal da tela.
- Secundário: usar `border-primary` e `text-primary` para ações alternativas ou de menor prioridade.
- Comportamento: altura mínima de `44px`, preferencialmente `48px`, com alvo de toque confortável.
- Evitar: mais de uma ação primária competindo na mesma tela.

**Inputs e campos de formulário**

- Uso: captura de nome, preço, quantidade, credenciais e códigos.
- Visual: usar `bg-surface`, `border-border`, `text-foreground` e `ring-ring` no foco.
- Erro: usar `border-destructive` e mensagem curta abaixo do campo.
- Comportamento: labels claros, teclado adequado ao tipo de dado e validação sem bloquear fluxos simples.
- Evitar: placeholders como única identificação do campo.

**Cards**

- Uso: representar itens manipuláveis, como templates, setores, produtos e compras no histórico.
- Visual: usar `bg-surface`, `border-border` e texto em `text-foreground`.
- Estado selecionado ou marcado: usar `bg-secondary` e destaque com `border-primary`.
- Comportamento: card deve ter uma ação principal clara e ações secundárias agrupadas em menu.
- Evitar: cards com muitos controles visíveis ao mesmo tempo.

**Itens de lista**

- Uso: listas densas de produtos, setores, histórico e opções de navegação.
- Visual: manter leitura vertical, divisores discretos e ícones Phosphor em `regular`.
- Comportamento: o texto principal deve ser escaneável rapidamente durante a compra.
- Evitar: depender de hover ou de ações escondidas para tarefas essenciais.

**Bottom Bar de Totais**

- Uso: manter subtotal e total acessíveis durante a sessão de compra.
- Visual: usar `bg-surface`, borda ou sombra superior discreta e total em Verde Folha.
- Comportamento: deve permanecer fácil de acessar no fluxo mobile e respeitar a largura do app shell.
- Evitar: transformar a barra em painel complexo com muitas ações.

**Bottom Navigation**

- Uso: navegação principal entre áreas recorrentes do app.
- Visual: usar `bg-surface`, ícones Phosphor e texto curto abaixo ou ao lado do ícone.
- Início: `HouseIcon`, navega para `T-004 Home (Dashboard)`.
- Templates: `ClipboardTextIcon` ou `ListChecksIcon`, navega para `T-005 Templates`.
- Compra: `ShoppingCartIcon` ou `BasketIcon`, inicia ou retoma uma sessão de compra.
- Histórico: `ClockCounterClockwiseIcon`, navega para `T-015 Histórico de Compras`.
- Configurações: `GearSixIcon`, navega para `T-021 Configurações`.
- O item ativo deve usar `text-primary`; itens inativos usam `text-muted-foreground`.
- Compra pode ter destaque visual como ação central, por ser o fluxo principal do app.
- Aparece nas telas principais autenticadas, dentro do app shell mobile.
- Não aparece em splash, login, cadastro, modais, bottom sheets ou fluxos focados.
- No desktop, continua dentro da largura fixa do app shell e não vira sidebar.

**FAB**

- Uso: ação rápida contextual, como adicionar item ou abrir ferramentas auxiliares.
- Visual: usar `bg-accent` e `text-accent-foreground`.
- Comportamento: deve ficar ao alcance do polegar e não cobrir informações críticas.
- Evitar: usar o FAB para ações destrutivas ou fluxos que exigem muita leitura.

**Modais e bottom sheets**

- Uso: confirmar ações, capturar dados curtos ou navegar por opções sem sair do fluxo atual.
- Visual: conteúdo em `bg-surface`, bordas suaves e overlay discreto quando necessário.
- Comportamento: em mobile, preferir bottom sheet para tarefas rápidas e modal para confirmação.
- Evitar: formulários longos ou fluxos profundos dentro de modais.

**Ações de gerenciamento**

- Editar nome: templates, setores e produtos usam popup simples com um único campo de nome.
- Excluir: sempre exige confirmação em popup, com opções "Sim" e "Não".
- Exclusão de template: avisar que a exclusão é permanente e remove setores e produtos em cascata.
- Exclusão de setor: avisar que a exclusão é permanente e remove os produtos daquele setor.
- Exclusão de produto: avisar que a exclusão é permanente e confirmar a ação.

**Estados vazios, erro e sucesso**

- Uso: explicar ausência de dados, falhas de validação, offline e conclusão de compra.
- Visual: usar `bg-secondary` para estados leves, `bg-destructive` para erros e Verde Folha para sucesso.
- Comportamento: toda mensagem deve indicar o que aconteceu e qual ação está disponível.
- Evitar: estados vazios genéricos sem próximo passo.

---

### 9.4 Descrição das Telas

**T-001: Splash Screen**

- Fundo: Verde Folha (`bg-primary`, `#2E7D32`)
- Logo ShopBud centralizado em Ivory Quente (`#FFF7EC`)
- Tagline abaixo: "Seu companheiro de compras"

---

**T-002: Login**

- Fundo: Ivory Quente (`bg-background`, `#FFF7EC`)
- Logo ShopBud no topo (menor)
- Card branco centralizado contendo:
    - Título: "Entrar"
    - Input de e-mail
    - Input de senha
    - Botão primário "Entrar"
    - Texto no rodapé: "Não tem conta? Criar conta"

---

**T-003: Cadastro**

- Similar ao Login
- Campos: Nome, E-mail, Senha, Confirmar Senha
- Botão primário "Criar conta"
- Texto no rodapé: "Já tem conta? Entrar"

---

**T-004: Home (Dashboard)**

- A Home é a tela inicial pós-login e não deve ser tratada como listagem de templates.
- Função: dar uma visão rápida do estado do app e oferecer atalhos para os fluxos principais.
- Header: Saudação ou título "Início" + acesso a configurações.
- Cards de resumo:
    - Quantidade de templates cadastrados
    - Quantidade de setores cadastrados
    - Quantidade de produtos cadastrados
    - Outros indicadores a definir, conforme o produto evoluir
- Atalhos principais:
    - Iniciar Compra
    - Ver Templates
    - Criar Template
    - Histórico
- A listagem completa de templates deve viver em uma tela própria, acessada pela Home.
- Evitar: transformar a Home em uma lista longa de templates. A Home pode mostrar resumos ou atalhos, mas não substitui a tela de templates.

---

**T-005: Templates (Listagem de Templates)**

- Função: listar, criar e gerenciar templates.
- Header: Título "Meus Templates" + ações de criar template e importar template.
- Lista de cards de template, cada um com:
    - Nome do template
    - Menu de ações: Editar, Visualizar, Compartilhar, Excluir
- Botão ou ação principal: "Criar Template"
- Criar Template: abre popup simples para informar o nome do template.
- Importar Template: ação global da tela, não vinculada a um template específico.
- Importar Template abre popup com input para colar o código de compartilhamento.
- Editar: abre popup simples para alterar o nome do template.
- Visualizar: navega para a tela de setores daquele template.
- Compartilhar: gera um código de compartilhamento do template para enviar a outra pessoa.
- Excluir: abre popup de confirmação informando que a exclusão é permanente e remove setores e produtos em cascata.
- Esta tela é separada da Home. Home mostra contexto e atalhos; Templates mostra a coleção completa.

---

**T-006: Gerenciar Setores do Template**

- Header: Seta voltar + Nome do template
- Lista de cards de setor, ordenados conforme a rota do supermercado
- Cada card mostra apenas o nome do setor e o menu de ações: Editar, Visualizar, Excluir
- Editar: abre popup simples para alterar o nome do setor.
- Visualizar: navega para a tela de produtos daquele setor.
- Excluir: abre popup de confirmação informando que a exclusão é permanente e remove os produtos daquele setor.
- Ordenação: drag-and-drop para reordenar os setores conforme a rota do supermercado
- Botão "+ Adicionar Setor" ao final da lista
- Adicionar Setor: abre popup simples para informar o nome do setor.

---

**T-007: Gerenciar Produtos do Setor**

- Header: Seta voltar + Nome do setor
- Lista de cards de produto ordenada por criação
- Cada card mostra apenas o nome do produto e o menu de ações: Editar, Excluir
- Editar: abre popup simples para alterar o nome do produto.
- Excluir: abre popup de confirmação informando que a exclusão é permanente.
- Botão "+ Adicionar Produto" ao final da lista
- Adicionar Produto: abre popup simples para informar o nome do produto.

---

**T-008: Selecionar Template para Compra**

- Modal ou tela com lista de templates disponíveis
- Cada item mostra o nome do template
- Ao clicar, inicia a sessão de compra

---

**T-009: Sessão de Compra - Setor Atual**

- Header: Nome do setor atual + indicador de progresso ("2 de 5 setores")
- Lista de produtos do setor:
    - Produtos não marcados: Card simples com nome
    - Produtos marcados: Card com borda Verde Folha, nome, preço e quantidade exibidos
- Ao clicar em produto não marcado: Abre modal para informar preço e quantidade
- Botão "+ Produto avulso" ao final da lista
- Bottom bar fixa: Subtotal do setor | Total da compra
- Navegação: Botões "Anterior" e "Próximo" ou swipe lateral
- Botão "Ver todos os setores" para navegação direta
- Se ficar offline após iniciar a sessão, exibe indicador discreto de conexão sem bloquear a compra

---

**T-010: Modal - Marcar Produto**

- Fundo escurecido
- Card branco centralizado:
    - Nome do produto (título)
    - Input: Preço (R$) com máscara monetária
    - Input: Quantidade (número)
    - Botões: "Cancelar" (secundário) | "Confirmar" (primário)

---

**T-011: Modal - Adicionar Produto Avulso**

- Similar ao anterior, com campo adicional:
    - Input: Nome do produto
    - Input: Preço
    - Input: Quantidade
    - Botões: "Cancelar" | "Adicionar"
    - O produto é adicionado apenas no setor atual da sessão

---

**T-012: Navegação de Setores**

- Modal ou bottom sheet
- Lista de todos os setores com indicadores:
    - Ícone de check Verde Folha: Setor visitado/completo
    - Destaque no setor atual
    - Contador de produtos marcados em cada setor
- Ao clicar, navega direto para o setor

---

**T-013: Finalizar Compra - Resumo**

- Header: "Resumo da Compra"
- Data e hora
- Nome do template usado
- Lista colapsável por setor:
    - Nome do setor + subtotal
    - Ao expandir: Lista de produtos com preço × quantidade
- Total geral em destaque (fonte grande, Verde Folha)
- Botões: "Voltar para Compra" (secundário) | "Finalizar" (primário)
- Se estiver offline ao finalizar, informa que a finalização ficará pendente de sincronização

---

**T-014: Compra Finalizada (Sucesso)**

- Ícone de check grande (Verde Folha)
- Mensagem: "Compra finalizada!"
- Total gasto em destaque
- Botões: "Ver no Histórico" | "Voltar ao Início"
- Quando a finalização estiver pendente de sincronização, exibe estado "Aguardando conexão" e não oferece acesso ao histórico remoto até sincronizar

---

**T-015: Histórico de Compras**

- Header: "Histórico"
- Lista de compras ordenada por data (mais recente primeiro)
- Cada item: Data + Nome do template + Total gasto
- Ao clicar, abre detalhes (T-016)
- Botão/aba para "Ver Evolução" (T-017)

---

**T-016: Detalhes da Compra (Histórico)**

- Similar ao resumo (T-013), mas em modo visualização
- Botão "Excluir" no header (com confirmação)

---

**T-017: Evolução de Gastos**

- Header: "Evolução de Gastos"
- Gráfico de linha ou barras mostrando gastos por mês
- Toggle para alternar entre "Total" e "Por Setor"
- Se "Por Setor": Seletor de setor específico
- Lista abaixo do gráfico com os valores detalhados

---

**T-018: Compartilhar Template**

- Modal após clicar em "Compartilhar":
    - Título: "Compartilhar Template"
    - Código gerado em destaque (ex: `SHP-A8K2M9`)
    - Texto: "Este código expira em 24 horas"
    - Botão "Copiar Código"
    - Botão "Fechar"

---

**T-019: Importar Template**

- Popup acionado pela ação global "Importar Template" na listagem de templates:
    - Título: "Importar Template"
    - Input para colar o código de compartilhamento
    - Botão "Buscar" ou "Pré-visualizar"
    - Após validar o código, mostra um preview do template copiado
    - Preview pendente de decisão: pode ser resumo com quantidade de setores e produtos, ou estrutura completa com setores e produtos por setor
    - O usuário deve confirmar antes de importar
    - Botão "Importar"

---

**T-020: Comparar Preços (Calculadora)**

- Acessível pelo FAB
- Título: "Comparar Preços"
- Dois cards empilhados no fluxo vertical do app:
    - Produto 1: Input preço + Input quantidade/peso
    - Produto 2: Input preço + Input quantidade/peso
- Seletor de unidade: un, L, ml, kg, g
- Resultado: "Produto 1: R$ X,XX por [unidade]" / "Produto 2: R$ X,XX por [unidade]"
- Destaque no melhor custo-benefício (badge verde "Melhor opção")

---

**T-021: Configurações**

- Header: "Configurações"
- Opções:
    - Conta (nome, e-mail)
    - Alterar senha
    - Sair (logout)
    - Sobre o app / Versão

---

### 9.5 Fluxo de Navegação

`Splash → Login/Cadastro → Home (Dashboard)
│
├── Templates/Setores/Produtos → Criar/Editar/Excluir (popups)
│
├── Templates → Visualizar Template → Setores → Visualizar Setor → Produtos
│
├── Iniciar Compra → Selecionar Template → Sessão de Compra → Resumo → Sucesso
│
├── Histórico → Detalhes / Evolução
│
├── Importar Template
│
└── Configurações

FAB (disponível em várias telas) → Comparar Preços`
