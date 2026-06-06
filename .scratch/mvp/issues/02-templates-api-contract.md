Status: ready-for-agent

# Implementar Templates proprios com contrato leve e detalhe estrutural

## What to build

Entregar o fluxo backend para o usuario autenticado criar, listar, abrir, renomear e excluir seus proprios Templates. A lista deve ser leve para selecao rapida, enquanto o detalhe deve expor a estrutura completa que o frontend usara para editar supermercado.

## API contract

- `GET /api/templates` retorna Templates do usuario com `id` e `name`, ordenados por `updated_at desc` por padrao, com opcao de ordenar por nome.
- `POST /api/templates` cria Template com `name` unico por usuario.
- `GET /api/templates/{template}` retorna `id`, `name`, `sectors[]` ordenados e `products[]` por criacao dentro de cada Sector.
- `PATCH /api/templates/{template}` renomeia Template.
- `DELETE /api/templates/{template}` remove Template, Sectors, Products e Shared Templates ativos, mas preserva Shopping Sessions e Purchase History.

## Acceptance criteria

- [ ] Template pertence obrigatoriamente a um usuario.
- [ ] Nome de Template e unico por usuario, mas pode repetir entre usuarios diferentes.
- [ ] Usuario nao consegue ver, renomear ou excluir Template de outro usuario.
- [ ] Listagem retorna apenas os campos leves esperados pelo frontend.
- [ ] Detalhe retorna estrutura completa mesmo quando nao ha Sectors.
- [ ] Exclusao remove estrutura do Template sem apagar historico de compras.
- [ ] Schema, model, policy, resource, controller/request e feature tests cobrem o comportamento.

## Blocked by

- 01-auth-api-contract.md

