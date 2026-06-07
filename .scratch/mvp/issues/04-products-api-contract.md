Status: accepted

# Implementar Products dentro de Sector

## What to build

Permitir que o usuario gerencie Products esperados dentro de cada Sector do Template. Products nao devem ter ordenacao manual; o contrato deve devolver uma ordem estavel por criacao.

## API contract

- `GET /api/templates/{template}/sectors/{sector}/products` lista Products do Sector por criacao.
- `POST /api/templates/{template}/sectors/{sector}/products` cria Product com `name`.
- `PATCH /api/templates/{template}/sectors/{sector}/products/{product}` renomeia Product.
- `DELETE /api/templates/{template}/sectors/{sector}/products/{product}` remove Product.

## Acceptance criteria

- [x] Product pertence obrigatoriamente a um Sector.
- [x] Nome de Product e unico dentro do Sector.
- [x] Products podem repetir em Sectors diferentes.
- [x] Listagem usa ordem de criacao e nao expoe reorder.
- [x] Usuario nao consegue operar Product fora de seus Templates.
- [x] Alteracoes em Products de Template nao afetam Shopping Sessions ja criadas.
- [x] Schema, model, resources, requests/controllers e feature tests cobrem o comportamento.

## Blocked by

- 03-sectors-api-contract.md

