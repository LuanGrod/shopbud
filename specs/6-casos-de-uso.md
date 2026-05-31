## 6. Casos de Uso

### UC-001: Criar Template

**Ator:** Usuário autenticado

**Pré-condições:** Usuário está logado no sistema

**Fluxo Principal:**

1. Usuário acessa a tela de templates
2. Usuário clica em "Novo Template"
3. Sistema exibe formulário solicitando o nome do template
4. Usuário informa o nome e confirma
5. Sistema valida que não existe outro template com o mesmo nome
6. Sistema cria o template e redireciona para a tela de edição do template

**Fluxos Alternativos:**

_5a. Já existe template com o mesmo nome:_

1. Sistema exibe mensagem de erro informando que já existe um template com esse nome
2. Usuário corrige o nome e confirma novamente

---

### UC-002: Iniciar Sessão de Compra

**Ator:** Usuário autenticado

**Pré-condições:** Usuário possui pelo menos um template com pelo menos um setor

**Fluxo Principal:**

1. Usuário acessa a tela inicial
2. Usuário clica em "Iniciar Compra"
3. Sistema exibe lista de templates disponíveis
4. Usuário seleciona um template
5. Sistema cria uma sessão de compra copiando os dados do template
6. Sistema redireciona para a tela de compra exibindo o primeiro setor

**Fluxos Alternativos:**

_4a. Usuário já possui uma sessão de compra ativa:_

1. Sistema pergunta se deseja continuar a compra existente ou descartá-la
2. Se continuar: sistema redireciona para a sessão existente
3. Se descartar: sistema exclui a sessão anterior e inicia uma nova

_4b. Template selecionado não possui setores:_

1. Sistema exibe mensagem informando que o template precisa ter pelo menos um setor
2. Sistema redireciona para edição do template

---

### UC-003: Marcar Produto Durante Compra

**Ator:** Usuário autenticado

**Pré-condições:** Usuário está em uma sessão de compra ativa

**Fluxo Principal:**

1. Usuário visualiza os produtos do setor atual
2. Usuário clica em um produto da lista
3. Sistema exibe campos para informar preço e quantidade
4. Usuário informa preço e quantidade e confirma
5. Sistema marca o produto como "pego"
6. Sistema atualiza o subtotal do setor e o total da compra

**Fluxos Alternativos:**

_4a. Usuário informa preço menor que R$ 0,01:_

1. Sistema exibe mensagem de erro
2. Usuário corrige o valor

_4b. Usuário informa quantidade menor que 1:_

1. Sistema exibe mensagem de erro
2. Usuário corrige o valor

---

### UC-004: Adicionar Produto Avulso

**Ator:** Usuário autenticado

**Pré-condições:** Usuário está em uma sessão de compra ativa

**Fluxo Principal:**

1. Usuário clica em "Adicionar Produto" no setor atual
2. Sistema exibe formulário solicitando nome, preço e quantidade
3. Sistema pergunta se deseja adicionar apenas nesta compra ou também no template
4. Usuário preenche os dados e seleciona a opção desejada
5. Sistema adiciona o produto na sessão atual
6. Se selecionou "também no template", sistema adiciona o produto no template original
7. Sistema atualiza os totais

---

### UC-005: Finalizar Compra

**Ator:** Usuário autenticado

**Pré-condições:** Usuário está em uma sessão de compra ativa

**Fluxo Principal:**

1. Usuário clica em "Finalizar Compra"
2. Sistema exibe tela de resumo com todos os produtos, subtotais por setor e total geral
3. Usuário confirma a finalização
4. Sistema salva a compra no histórico com data/hora atual
5. Sistema encerra a sessão de compra
6. Sistema redireciona para a tela de resumo final

**Fluxos Alternativos:**

_2a. Nenhum produto foi marcado:_

1. Sistema exibe aviso informando que nenhum produto foi marcado
2. Sistema pergunta se deseja finalizar mesmo assim ou voltar para a compra
3. Se finalizar: continua o fluxo principal
4. Se voltar: retorna para a sessão de compra

---

### UC-006: Comparar Preços

**Ator:** Usuário autenticado

**Pré-condições:** Nenhuma

**Fluxo Principal:**

1. Usuário clica no botão "+" e seleciona "Comparar Preços"
2. Sistema exibe formulário com campos para dois produtos
3. Usuário informa preço e quantidade/peso/volume do primeiro produto
4. Usuário informa preço e quantidade/peso/volume do segundo produto
5. Usuário seleciona a unidade de medida (un, L, kg, ml, g)
6. Sistema calcula o preço por unidade de cada produto
7. Sistema exibe qual produto tem melhor custo-benefício

---

### UC-007: Visualizar Lista de Compras Anteriores

**Ator:** Usuário autenticado

**Pré-condições:** Usuário está logado no sistema

**Fluxo Principal:**

1. Usuário acessa a tela de histórico
2. Sistema exibe lista de compras anteriores ordenadas por data (mais recente primeiro)
3. Cada item da lista exibe: data da compra, nome do template utilizado e total gasto

---

### UC-008: Visualizar Detalhes de uma Compra

**Ator:** Usuário autenticado

**Pré-condições:** Existe pelo menos uma compra no histórico

**Fluxo Principal:**

1. Usuário acessa a tela de histórico
2. Usuário clica em uma compra da lista
3. Sistema exibe os detalhes da compra: data/hora, template utilizado, lista de setores com seus produtos (nome, preço, quantidade), subtotal por setor e total geral

---

### UC-009: Excluir Compra do Histórico

**Ator:** Usuário autenticado

**Pré-condições:** Existe pelo menos uma compra no histórico

**Fluxo Principal:**

1. Usuário acessa os detalhes de uma compra
2. Usuário clica em "Excluir"
3. Sistema solicita confirmação
4. Usuário confirma
5. Sistema remove a compra do histórico

**Fluxos Alternativos:**

_4a. Usuário cancela:_

1. Sistema mantém a compra e retorna para a tela anterior

---

### UC-029: Visualizar Evolução de Gastos

**Ator:** Usuário autenticado

**Pré-condições:** Existe pelo menos uma compra no histórico

**Fluxo Principal:**

1. Usuário acessa a tela de histórico
2. Usuário clica em "Ver Evolução"
3. Sistema exibe gráfico/lista com a evolução dos gastos totais ao longo do tempo
4. Usuário pode alternar para visualizar evolução por setor específico

---

### UC-010: Exportar Template

**Ator:** Usuário autenticado

**Pré-condições:** Usuário possui pelo menos um template

**Fluxo Principal:**

1. Usuário acessa a tela de templates
2. Usuário seleciona um template e clica em "Compartilhar"
3. Sistema gera uma cópia congelada do template
4. Sistema gera um código único de compartilhamento com validade de 24 horas
5. Sistema exibe o código para o usuário copiar

---

### UC-011: Importar Template

**Ator:** Usuário autenticado

**Pré-condições:** Usuário possui um código de compartilhamento válido

**Fluxo Principal:**

1. Usuário acessa a tela de templates
2. Usuário clica em "Importar Template"
3. Sistema solicita o código de compartilhamento
4. Usuário informa o código
5. Sistema valida o código e busca o template associado
6. Sistema exibe preview do template (nome, setores, quantidade de produtos)
7. Usuário confirma a importação
8. Sistema cria uma cópia do template para o usuário
9. Sistema redireciona para a lista de templates

**Fluxos Alternativos:**

_5a. Código inválido ou expirado:_

1. Sistema exibe mensagem informando que o código é inválido ou expirou
2. Usuário pode tentar outro código

_8a. Já existe template com o mesmo nome:_

1. Sistema sugere um novo nome (ex: "Nome do Template (2)")
2. Usuário pode aceitar ou informar outro nome
3. Sistema cria o template com o nome definido

---

### UC-012: Sincronizar Dados Após Reconexão

**Ator:** Sistema

**Pré-condições:** Existem dados locais pendentes de sincronização e conexão foi restabelecida

**Fluxo Principal:**

1. Sistema detecta que a conexão com a internet foi restabelecida
2. Sistema identifica dados marcados como "pendentes de sincronização"
3. Sistema envia os dados para o servidor
4. Servidor processa e armazena os dados
5. Sistema marca os dados locais como sincronizados
6. Sistema notifica o usuário discretamente que a sincronização foi concluída

**Fluxos Alternativos:**

_4a. Conflito de dados (mesmo registro alterado local e remotamente):_

1. Sistema compara as datas de modificação
2. Sistema mantém a versão mais recente
3. Sistema registra o conflito para debug (opcional)

_3a. Falha na sincronização:_

1. Sistema mantém os dados como "pendentes"
2. Sistema tenta novamente em alguns minutos
