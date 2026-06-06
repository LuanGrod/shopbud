Status: ready-for-agent

# Implementar Sectors ordenados dentro de Template

## What to build

Permitir que o usuario modele a rota real de um Template criando, renomeando, removendo e reordenando Sectors. O backend deve manter a ordem sempre normalizada e impedir operacoes fora da propriedade do usuario.

## API contract

- `GET /api/templates/{template}/sectors` lista Sectors do Template em ordem de rota.
- `POST /api/templates/{template}/sectors` cria Sector no fim da rota.
- `PATCH /api/templates/{template}/sectors/{sector}` renomeia Sector.
- `DELETE /api/templates/{template}/sectors/{sector}` remove Sector e seus Products.
- `PUT /api/templates/{template}/sectors/reorder` recebe lista completa de ids e persiste ordem normalizada `1..n`.

## Acceptance criteria

- [ ] Sector pertence obrigatoriamente a um Template.
- [ ] Nome de Sector e unico dentro do Template.
- [ ] Novo Sector e sempre acrescentado ao fim da rota atual.
- [ ] Reorder exige todos e somente os Sectors do Template.
- [ ] Ordem persistida fica normalizada sem lacunas ou duplicatas.
- [ ] Excluir Sector exclui seus Products.
- [ ] Usuario nao consegue operar Sectors de Template alheio.
- [ ] Feature tests cobrem criacao, validacao, rename, delete cascade e reorder.

## Blocked by

- 02-templates-api-contract.md

