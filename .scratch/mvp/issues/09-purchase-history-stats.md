Status: ready-for-agent

# Expor stats de evolucao de gastos

## What to build

Fornecer agregacoes backend-only para o frontend exibir evolucao de gastos ao longo do tempo e concentracao por Sector, usando somente Purchase History do usuario autenticado.

## API contract

- `GET /api/purchase-histories/stats/evolution` retorna pontos temporais com data/periodo e total.
- `GET /api/purchase-histories/stats/sectors` retorna totais agregados por nome de Sector.
- Endpoints aceitam filtros simples de periodo se ja houver convencao local; caso contrario, entregar contrato minimo sem filtro.

## Acceptance criteria

- [ ] Stats usam apenas Purchase Histories do usuario autenticado.
- [ ] Evolucao temporal e ordenada cronologicamente.
- [ ] Agregado por Sector soma subtotais oficiais persistidos no historico.
- [ ] Compras vazias nao quebram os agregados.
- [ ] Contratos JSON sao estaveis e testados.
- [ ] Feature tests cobrem isolamento por usuario e calculo com multiplas compras.

## Blocked by

- 08-purchase-history-api-contract.md

