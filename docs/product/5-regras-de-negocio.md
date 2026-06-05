## 5. Regras de Negócio

### 5.1 Templates

- **RN-001:** Um template deve ter pelo menos um setor para poder ser utilizado em uma sessão de compra
- **RN-002:** Um setor pode existir sem produtos cadastrados
- **RN-003:** Não é permitido criar dois templates com o mesmo nome para o mesmo usuário
- **RN-004:** Ao excluir um template, todos os setores, produtos e compartilhamentos ativos associados a ele devem ser excluídos
- **RN-005:** Ao excluir um setor, todos os produtos associados a ele devem ser excluídos
- **RN-006:** Não é permitido criar dois setores com o mesmo nome dentro do mesmo template
- **RN-007:** Não é permitido criar dois produtos com o mesmo nome dentro do mesmo setor
- **RN-008:** Um novo setor deve ser adicionado ao final da ordem atual do template
- **RN-009:** Ao reordenar setores, o backend deve normalizar a ordem para uma sequência contínua por template

### 5.2 Sessão de Compra

- **RN-010:** Uma sessão de compra só pode ser iniciada a partir de um template existente
- **RN-011:** Uma sessão de compra só pode ser iniciada com conexão com a internet
- **RN-012:** Ao iniciar uma sessão de compra, os setores e produtos do template são copiados para um snapshot da sessão
- **RN-013:** Alterações posteriores no template não afetam sessões de compra já iniciadas
- **RN-014:** Alterações feitas durante uma sessão de compra não afetam o template original
- **RN-015:** O backend deve persistir a sessão `active` e seu snapshot ao iniciar a compra, mas não deve persistir cada item marcado durante a compra
- **RN-016:** O frontend controla localmente navegação, itens marcados, produtos avulsos, subtotais e total temporário durante a compra
- **RN-017:** O usuário pode finalizar uma compra mesmo sem ter marcado nenhum produto
- **RN-018:** Ao adicionar um produto avulso durante a compra, ele deve ser salvo apenas na sessão atual
- **RN-019:** Produtos avulsos só podem ser adicionados dentro de setores existentes no snapshot da sessão
- **RN-020:** Não é permitido criar setores durante uma sessão de compra
- **RN-021:** Se faltar um setor durante a compra, o usuário deve cancelar a sessão, alterar o template e iniciar uma nova sessão
- **RN-022:** Produtos enviados na finalização devem obrigatoriamente ter preço e quantidade informados
- **RN-023:** O preço mínimo de um produto é R$ 0,01
- **RN-024:** A quantidade mínima de um produto é 1
- **RN-025:** O backend deve recalcular o subtotal do setor como a soma de (preço × quantidade) de todos os produtos finalizados naquele setor
- **RN-026:** O backend deve recalcular o total da compra como a soma dos subtotais de todos os setores
- **RN-027:** Uma sessão `active` com mais de 24 horas deve ser cancelada automaticamente
- **RN-028:** Se o usuário tentar iniciar uma nova sessão e existir uma sessão `active` expirada, o backend deve cancelar a sessão expirada e criar uma nova
- **RN-029:** Se o usuário tentar iniciar uma nova sessão e existir uma sessão `active` válida, o backend deve retornar a sessão existente sem criar outra
- **RN-030:** Uma finalização offline sincronizada depois que a sessão expirou ou foi cancelada deve ser rejeitada

### 5.3 Histórico

- **RN-031:** Uma compra só é salva no histórico após ser finalizada
- **RN-032:** O histórico de compras não pode ser editado, apenas visualizado
- **RN-033:** O usuário pode excluir uma compra do histórico
- **RN-034:** Os dados do histórico devem incluir: data/hora da finalização, template utilizado, setores, produtos com preço e quantidade, subtotais por setor e total geral

### **5.4 Compartilhamento de Templates**

- **RN-035:** Ao compartilhar um template, o sistema deve gerar um código único de compartilhamento
- **RN-036:** O código de compartilhamento expira em 24 horas após a geração
- **RN-037:** O template compartilhado é uma cópia congelada do estado no momento do compartilhamento
- **RN-038:** Alterações no template original após o compartilhamento não afetam o snapshot compartilhado
- **RN-039:** Ao excluir o template original, seus códigos de compartilhamento ativos devem ser invalidados
- **RN-040:** Ao importar um template, se já existir um com o mesmo nome, o sistema deve sugerir um novo nome (ex: "Nome do Template (2)")
- **RN-041:** Após a importação, o template pertence ao usuário que importou e é independente do original
- **RN-042:** Um código expirado ou revogado não pode ser utilizado para importação

### 5.5 Calculadora de Comparação

- **RN-043:** A comparação deve calcular o preço por unidade de medida (por unidade, litro, kg, ml, g) para determinar o melhor custo-benefício
- **RN-044:** O usuário deve informar a mesma unidade de medida para os dois produtos comparados

### 5.6 Sincronização Offline

- **RN-045:** Sessões de compra não podem ser iniciadas offline
- **RN-046:** Uma sessão iniciada online pode continuar localmente offline
- **RN-047:** Se uma sessão for finalizada offline, o frontend deve registrar uma operação pendente `finish_session` com o identificador da sessão e os itens finais
- **RN-048:** Ao restabelecer conexão, o frontend deve enviar a operação `finish_session` pendente para o backend
- **RN-049:** O backend deve rejeitar `finish_session` de sessões expiradas ou canceladas
