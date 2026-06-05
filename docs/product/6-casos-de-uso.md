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
5. Sistema cria no backend uma sessão de compra `active`, copiando os setores e produtos atuais do template para um snapshot
6. Sistema redireciona para a tela de compra exibindo o primeiro setor

**Fluxos Alternativos:**

_4a. Usuário já possui uma sessão de compra ativa válida:_

1. Sistema retorna a sessão ativa existente
2. Interface pergunta se deseja continuar a compra existente ou cancelá-la para iniciar outra
3. Se continuar: sistema redireciona para a sessão existente
4. Se cancelar: sistema cancela a sessão anterior e permite iniciar uma nova

_4b. Usuário possui uma sessão ativa expirada:_

1. Sistema cancela automaticamente a sessão expirada
2. Sistema cria uma nova sessão de compra

_4c. Template selecionado não possui setores:_

1. Sistema exibe mensagem informando que o template precisa ter pelo menos um setor
2. Sistema redireciona para edição do template

_4d. Usuário está offline:_

1. Sistema informa que é necessário estar online para iniciar a sessão de compra

---

### UC-003: Marcar Produto Durante Compra

**Ator:** Usuário autenticado

**Pré-condições:** Usuário está em uma sessão de compra ativa já iniciada online

**Fluxo Principal:**

1. Usuário visualiza os produtos do setor atual
2. Usuário clica em um produto da lista
3. Sistema exibe campos para informar preço e quantidade
4. Usuário informa preço e quantidade e confirma
5. Frontend marca o produto como "pego" localmente
6. Frontend atualiza o subtotal do setor e o total temporário da compra

**Fluxos Alternativos:**

_4a. Usuário informa preço menor que R$ 0,01:_

1. Sistema exibe mensagem de erro
2. Usuário corrige o valor

_4b. Usuário informa quantidade menor que 1:_

1. Sistema exibe mensagem de erro
2. Usuário corrige o valor

**Regras:**

- Marcar, desmarcar e editar produtos durante a compra não envia alterações item-a-item para o backend
- O backend persiste os itens apenas na finalização da sessão

---

### UC-004: Adicionar Produto Avulso

**Ator:** Usuário autenticado

**Pré-condições:** Usuário está em uma sessão de compra ativa já iniciada online

**Fluxo Principal:**

1. Usuário clica em "Adicionar Produto" no setor atual
2. Sistema exibe formulário solicitando nome, preço e quantidade
3. Usuário preenche os dados e confirma
4. Frontend adiciona o produto apenas no setor atual da sessão
5. Frontend atualiza o subtotal do setor e o total temporário

**Regras:**

- O produto avulso não altera o template original
- O produto avulso deve ser adicionado dentro de um setor existente no snapshot da sessão
- Se faltar um setor, o usuário deve cancelar a sessão, alterar o template e iniciar uma nova sessão
- O produto avulso só é enviado ao backend na finalização da sessão

---

### UC-005: Finalizar Compra

**Ator:** Usuário autenticado

**Pré-condições:** Usuário está em uma sessão de compra ativa

**Fluxo Principal:**

1. Usuário clica em "Finalizar Compra"
2. Sistema exibe tela de resumo local com todos os produtos, subtotais por setor e total temporário
3. Usuário confirma a finalização
4. Frontend envia ao backend a lista final de itens da sessão
5. Backend valida a sessão, os setores do snapshot, os produtos, preços e quantidades
6. Backend recalcula subtotais por setor e total geral
7. Backend persiste os Shopping Items e salva a compra no histórico com data/hora atual
8. Backend encerra a sessão de compra com status `finished`
9. Sistema redireciona para a tela de resumo final

**Fluxos Alternativos:**

_2a. Nenhum produto foi marcado:_

1. Sistema exibe aviso informando que nenhum produto foi marcado
2. Sistema pergunta se deseja finalizar mesmo assim ou voltar para a compra
3. Se finalizar: continua o fluxo principal
4. Se voltar: retorna para a sessão de compra

_4a. Usuário está offline:_

1. Frontend salva uma operação pendente `finish_session` com o identificador da sessão e a lista final de itens
2. Frontend informa que a finalização será sincronizada quando a conexão voltar

_5a. Sessão expirou ou foi cancelada antes da sincronização:_

1. Backend rejeita a finalização
2. Frontend descarta automaticamente a cópia local da finalização pendente

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

### UC-010: Visualizar Evolução de Gastos

**Ator:** Usuário autenticado

**Pré-condições:** Existe pelo menos uma compra no histórico

**Fluxo Principal:**

1. Usuário acessa a tela de histórico
2. Usuário clica em "Ver Evolução"
3. Sistema exibe gráfico/lista com a evolução dos gastos totais ao longo do tempo
4. Usuário pode alternar para visualizar evolução por setor específico

---

### UC-011: Compartilhar Template

**Ator:** Usuário autenticado

**Pré-condições:** Usuário possui pelo menos um template

**Fluxo Principal:**

1. Usuário acessa a tela de templates
2. Usuário seleciona um template e clica em "Compartilhar"
3. Sistema gera um snapshot do template
4. Sistema gera um código único de compartilhamento com validade de 24 horas
5. Sistema exibe o código para o usuário copiar

**Fluxos Alternativos:**

_3a. Template original foi excluído:_

1. Sistema invalida códigos ativos associados ao template excluído
2. Códigos revogados não podem ser usados para importação

---

### UC-012: Importar Template

**Ator:** Usuário autenticado

**Pré-condições:** Usuário possui um código de compartilhamento válido

**Fluxo Principal:**

1. Usuário acessa a tela de templates
2. Usuário clica em "Importar Template"
3. Sistema solicita o código de compartilhamento
4. Usuário informa o código
5. Sistema valida que o código existe, não expirou e não foi revogado
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

### UC-013: Sincronizar Finalização Após Reconexão

**Ator:** Sistema

**Pré-condições:** Existe uma operação local `finish_session` pendente e a conexão foi restabelecida

**Fluxo Principal:**

1. Sistema detecta que a conexão com a internet foi restabelecida
2. Sistema identifica a operação pendente `finish_session`
3. Sistema envia ao backend o identificador da sessão e a lista final de itens
4. Backend valida que a sessão existe, pertence ao usuário e ainda não expirou
5. Backend recalcula subtotais e total
6. Backend persiste os Shopping Items, cria o histórico e marca a sessão como `finished`
7. Sistema remove a operação pendente local
8. Sistema notifica o usuário discretamente que a sincronização foi concluída

**Fluxos Alternativos:**

_4a. Sessão expirou ou foi cancelada:_

1. Backend rejeita a finalização
2. Sistema remove a operação pendente local

_3a. Falha na sincronização:_

1. Sistema mantém a operação como pendente
2. Sistema tenta novamente em alguns minutos
