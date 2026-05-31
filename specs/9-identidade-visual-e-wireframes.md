## 9. Identidade Visual e Wireframes

### 9.1 Identidade Visual

**Nome:** ShopBud

**Tagline:** "Seu companheiro de compras"

**Tom de voz:** Amigável, direto e encorajador. O app fala com o usuário de forma simples, sem termos técnicos, como um amigo ajudando nas compras.

**Paleta de Cores:**

| Função           | Cor              | Hex       | Uso                                              |
| ---------------- | ---------------- | --------- | ------------------------------------------------ |
| Primária         | Verde Menta      | `#2ECC71` | Botões principais, header, elementos de destaque |
| Secundária       | Verde Escuro     | `#27AE60` | Hover, bordas, ícones ativos                     |
| Destaque         | Laranja          | `#F39C12` | CTAs importantes, badges, alertas de atenção     |
| Fundo            | Cinza Esverdeado | `#F8FAF9` | Background geral das telas                       |
| Superfície       | Branco           | `#FFFFFF` | Cards, modais, inputs                            |
| Texto Principal  | Cinza Escuro     | `#2C3E50` | Títulos, textos de corpo                         |
| Texto Secundário | Cinza Médio      | `#7F8C8D` | Labels, placeholders, textos auxiliares          |
| Erro             | Vermelho         | `#E74C3C` | Mensagens de erro, validações                    |
| Sucesso          | Verde            | `#27AE60` | Confirmações, produto marcado                    |

**Tipografia:**

- Títulos: Inter Bold (ou similar sans-serif), 20-24px
- Corpo: Inter Regular, 16px
- Auxiliar: Inter Medium, 14px
- Valores monetários: Inter Semi-Bold, para destaque

**Iconografia:**

- Estilo: Outlined, traços arredondados
- Tamanho padrão: 24px
- Biblioteca sugerida: Lucide Icons ou Phosphor Icons

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

### 9.2 Componentes Principais

**Botão Primário:**

- Fundo: Verde Menta (`#2ECC71`)
- Texto: Branco, 16px, Semi-Bold
- Altura: 48px
- Border radius: 12px
- Hover: Verde Escuro (`#27AE60`)
- Disabled: Opacidade 50%

**Botão Secundário:**

- Fundo: Transparente
- Borda: 2px Verde Menta
- Texto: Verde Menta, 16px, Semi-Bold
- Altura: 48px
- Border radius: 12px

**Input de Texto:**

- Fundo: Branco
- Borda: 1px `#E0E0E0`
- Border radius: 12px
- Altura: 48px
- Padding: 16px
- Foco: Borda Verde Menta
- Erro: Borda Vermelha + mensagem abaixo

**Card de Produto:**

- Fundo: Branco
- Border radius: 12px
- Padding: 16px
- Sombra sutil
- Conteúdo: Nome do produto à esquerda, checkbox ou info de preço à direita
- Estado marcado: Borda Verde Menta à esquerda (4px) + fundo levemente esverdeado (`#F0FDF4`)

**Card de Setor:**

- Fundo: Branco
- Border radius: 12px
- Padding: 16px
- Ícone de drag (≡) à esquerda para reordenação
- Nome do setor ao centro
- Contador de produtos à direita

**Bottom Bar de Totais:**

- Fixo no rodapé durante sessão de compra
- Fundo: Branco
- Sombra superior: `0 -2px 8px rgba(0,0,0,0.08)`
- Duas colunas: "Setor: R$ XX,XX" | "Total: R$ XX,XX"
- Total em destaque (Verde Menta, fonte maior)

**FAB (Floating Action Button):**

- Posição: Canto inferior direito
- Fundo: Laranja (`#F39C12`)
- Ícone: + branco
- Tamanho: 56px
- Border radius: 50%
- Sombra: `0 4px 12px rgba(0,0,0,0.15)`
- Ao clicar: Expande menu com opções (Comparar Preços, etc)

---

### 9.3 Descrição das Telas

**T-001: Splash Screen**

- Fundo: Verde Menta (`#2ECC71`)
- Logo ShopBud centralizado em branco
- Tagline abaixo: "Seu companheiro de compras"

---

**T-002: Login**

- Fundo: `#F8FAF9`
- Logo ShopBud no topo (menor)
- Card branco centralizado contendo:
    - Título: "Entrar"
    - Input de e-mail
    - Input de senha
    - Link "Esqueci minha senha"
    - Botão primário "Entrar"
    - Divisor "ou"
    - Botão secundário com ícone Google "Entrar com Google"
    - Texto no rodapé: "Não tem conta? Criar conta"

---

**T-003: Cadastro**

- Similar ao Login
- Campos: Nome, E-mail, Senha, Confirmar Senha
- Botão primário "Criar conta"
- Texto no rodapé: "Já tem conta? Entrar"

---

**T-004: Home (Lista de Templates)**

- Header: Título "Meus Templates" + ícone de configurações
- Lista de cards de template, cada um com:
    - Nome do template
    - Quantidade de setores
    - Menu de ações (três pontos): Editar, Compartilhar, Excluir
- Botão primário fixo no rodapé: "Iniciar Compra"
- FAB no canto inferior direito para criar novo template

---

**T-005: Criar/Editar Template**

- Header: Seta voltar + Título "Novo Template" ou "Editar Template"
- Input: Nome do template
- Seção "Setores":
    - Lista de cards de setor (draggable para reordenar)
    - Botão "+ Adicionar Setor" ao final da lista
- Botão primário no rodapé: "Salvar"

---

**T-006: Editar Setor**

- Header: Seta voltar + Nome do setor (editável)
- Lista de cards de produto (draggable para reordenar)
- Cada card: Nome do produto + ícone de excluir
- Botão "+ Adicionar Produto" ao final da lista
- Alterações salvam automaticamente ou botão "Salvar"

---

**T-007: Selecionar Template para Compra**

- Modal ou tela com lista de templates disponíveis
- Cada item mostra: Nome + quantidade de setores + quantidade de produtos
- Ao clicar, inicia a sessão de compra

---

**T-008: Sessão de Compra - Setor Atual**

- Header: Nome do setor atual + indicador de progresso ("2 de 5 setores")
- Lista de produtos do setor:
    - Produtos não marcados: Card simples com nome
    - Produtos marcados: Card com borda verde, nome, preço e quantidade exibidos
- Ao clicar em produto não marcado: Abre modal para informar preço e quantidade
- Botão "+ Produto avulso" ao final da lista
- Bottom bar fixa: Subtotal do setor | Total da compra
- Navegação: Botões "Anterior" e "Próximo" ou swipe lateral
- Botão "Ver todos os setores" para navegação direta

---

**T-009: Modal - Marcar Produto**

- Fundo escurecido
- Card branco centralizado:
    - Nome do produto (título)
    - Input: Preço (R$) com máscara monetária
    - Input: Quantidade (número)
    - Botões: "Cancelar" (secundário) | "Confirmar" (primário)

---

**T-010: Modal - Adicionar Produto Avulso**

- Similar ao anterior, com campo adicional:
    - Input: Nome do produto
    - Input: Preço
    - Input: Quantidade
    - Toggle/Checkbox: "Adicionar também ao template"
    - Botões: "Cancelar" | "Adicionar"

---

**T-011: Navegação de Setores**

- Modal ou bottom sheet
- Lista de todos os setores com indicadores:
    - Ícone de check verde: Setor visitado/completo
    - Destaque no setor atual
    - Contador de produtos marcados em cada setor
- Ao clicar, navega direto para o setor

---

**T-012: Finalizar Compra - Resumo**

- Header: "Resumo da Compra"
- Data e hora
- Nome do template usado
- Lista colapsável por setor:
    - Nome do setor + subtotal
    - Ao expandir: Lista de produtos com preço × quantidade
- Total geral em destaque (fonte grande, Verde Menta)
- Botões: "Voltar para Compra" (secundário) | "Finalizar" (primário)

---

**T-013: Compra Finalizada (Sucesso)**

- Ícone de check grande (verde)
- Mensagem: "Compra finalizada!"
- Total gasto em destaque
- Botões: "Ver no Histórico" | "Voltar ao Início"

---

**T-014: Histórico de Compras**

- Header: "Histórico"
- Lista de compras ordenada por data (mais recente primeiro)
- Cada item: Data + Nome do template + Total gasto
- Ao clicar, abre detalhes (T-015)
- Botão/aba para "Ver Evolução" (T-016)

---

**T-015: Detalhes da Compra (Histórico)**

- Similar ao resumo (T-012), mas em modo visualização
- Botão "Excluir" no header (com confirmação)

---

**T-016: Evolução de Gastos**

- Header: "Evolução de Gastos"
- Gráfico de linha ou barras mostrando gastos por mês
- Toggle para alternar entre "Total" e "Por Setor"
- Se "Por Setor": Seletor de setor específico
- Lista abaixo do gráfico com os valores detalhados

---

**T-017: Compartilhar Template**

- Modal após clicar em "Compartilhar":
    - Título: "Compartilhar Template"
    - Código gerado em destaque (ex: `SHP-A8K2M9`)
    - Texto: "Este código expira em 24 horas"
    - Botão "Copiar Código"
    - Botão "Fechar"

---

**T-018: Importar Template**

- Modal ao clicar em "Importar":
    - Título: "Importar Template"
    - Input: Código de compartilhamento
    - Botão "Buscar"
    - Após buscar, mostra preview: Nome, setores, quantidade de produtos
    - Botão "Importar"

---

**T-019: Comparar Preços (Calculadora)**

- Acessível pelo FAB
- Título: "Comparar Preços"
- Dois cards lado a lado (ou empilhados no mobile):
    - Produto 1: Input preço + Input quantidade/peso
    - Produto 2: Input preço + Input quantidade/peso
- Seletor de unidade: un, L, ml, kg, g
- Resultado: "Produto 1: R$ X,XX por [unidade]" / "Produto 2: R$ X,XX por [unidade]"
- Destaque no melhor custo-benefício (badge verde "Melhor opção")

---

**T-020: Configurações**

- Header: "Configurações"
- Opções:
    - Conta (nome, e-mail)
    - Alterar senha
    - Sair (logout)
    - Sobre o app / Versão

---

### 9.4 Fluxo de Navegação

`Splash → Login/Cadastro → Home (Templates)
│
├── Criar/Editar Template → Editar Setor
│
├── Iniciar Compra → Selecionar Template → Sessão de Compra → Resumo → Sucesso
│
├── Histórico → Detalhes / Evolução
│
├── Importar Template
│
└── Configurações

FAB (disponível em várias telas) → Comparar Preços`
