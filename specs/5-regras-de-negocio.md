## 5. Regras de Negócio

### 5.1 Templates

- **RN-001:** Um template deve ter pelo menos um setor para poder ser utilizado em uma sessão de compra
- **RN-002:** Um setor pode existir sem produtos cadastrados
- **RN-003:** Não é permitido criar dois templates com o mesmo nome para o mesmo usuário
- **RN-004:** Ao excluir um template, todos os setores e produtos associados a ele devem ser excluídos
- **RN-005:** Ao excluir um setor, todos os produtos associados a ele devem ser excluídos

### 5.2 Sessão de Compra

- **RN-006:** Uma sessão de compra só pode ser iniciada a partir de um template existente
- **RN-007:** O usuário pode ter apenas uma sessão de compra ativa por vez
- **RN-008:** Ao iniciar uma sessão de compra, os dados do template são copiados para a sessão, permitindo alterações sem afetar o template original
- **RN-009:** O usuário pode finalizar uma compra mesmo sem ter marcado nenhum produto
- **RN-010:** Ao adicionar um produto avulso "apenas nesta compra", ele não deve ser salvo no template original
- **RN-011:** Ao adicionar um produto avulso "também no template", ele deve ser salvo tanto na sessão atual quanto no template original
- **RN-012:** Produtos marcados devem obrigatoriamente ter preço e quantidade informados
- **RN-013:** O preço mínimo de um produto é R$ 0,01
- **RN-014:** A quantidade mínima de um produto é 1
- **RN-015:** O subtotal do setor é a soma de (preço × quantidade) de todos os produtos marcados naquele setor
- **RN-016:** O total da compra é a soma dos subtotais de todos os setores

### 5.3 Histórico

- **RN-017:** Uma compra só é salva no histórico após ser finalizada
- **RN-018:** O histórico de compras não pode ser editado, apenas visualizado
- **RN-019:** O usuário pode excluir uma compra do histórico
- **RN-020:** Os dados do histórico devem incluir: data/hora da finalização, template utilizado, setores, produtos com preço e quantidade, subtotais por setor e total geral

### **5.4 Compartilhamento de Templates**

- **RN-021:** Ao exportar um template, o sistema deve gerar um código único de compartilhamento
- **RN-022:** O código de compartilhamento expira em 24 horas após a geração
- **RN-023:** O template exportado é uma cópia congelada do estado no momento da exportação
- **RN-024:** Alterações no template original após a exportação não afetam o template compartilhado
- **RN-025:** Ao importar um template, se já existir um com o mesmo nome, o sistema deve sugerir um novo nome (ex: "Nome do Template (2)")
- **RN-026:** Após a importação, o template pertence ao usuário que importou e é independente do original
- **RN-027:** Um código expirado não pode ser utilizado para importação

### 5.5 Calculadora de Comparação

- **RN-028:** A comparação deve calcular o preço por unidade de medida (por unidade, litro, kg, ml, g) para determinar o melhor custo-benefício
- **RN-029:** O usuário deve informar a mesma unidade de medida para os dois produtos comparados

### 5.6 Sincronização Offline

- **RN-030:** Dados criados offline devem ser marcados como "pendentes de sincronização"
- **RN-031:** Em caso de conflito entre dados locais e remotos, os dados com a data de modificação mais recente prevalecem
- **RN-032:** Se uma sessão de compra for iniciada offline, ela deve ser sincronizada por completo ao restabelecer conexão
