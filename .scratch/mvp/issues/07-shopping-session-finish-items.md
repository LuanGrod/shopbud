Status: accepted

# Finalizar Shopping Session e persistir Shopping Items oficiais

## What to build

Implementar a finalizacao oficial de uma Shopping Session ativa. O frontend envia o estado final local, e o backend valida, persiste Shopping Items e recalcula subtotais e total sem confiar nos calculos do cliente.

## API contract

- `POST /api/shopping-sessions/{shoppingSession}/finish` recebe `items[]`.
- Cada item contem `sector_snapshot_id` ou identificador estavel do Sector no Snapshot, `product_name`, `price`, `quantity`, `extra`.
- `price` e obrigatorio para item finalizado e deve ser no minimo `0.01`.
- `quantity` e obrigatorio para item finalizado e deve ser no minimo `1`.
- Payload vazio e valido e finaliza compra com total `0`.
- Resposta retorna resumo oficial com sectors, items, subtotals e total.

## Acceptance criteria

- [x] Apenas usuario dono finaliza sua propria sessao ativa nao expirada.
- [x] Finish rejeita sessao cancelada, finalizada ou expirada.
- [x] Item so pode pertencer a Sector existente no Snapshot.
- [x] Product avulso e salvo como Shopping Item com `extra=true` dentro de Sector existente no Snapshot.
- [x] Nenhum Shopping Item e persistido antes da finalizacao.
- [x] Backend recalcula subtotal por Sector e total geral a partir de `price * quantity`.
- [x] Finalizacao altera status da sessao para `finished`.
- [x] Feature tests cobrem payload valido, vazio, validacoes minimas, extra e nao mutacao do Template.

## Blocked by

- 05-shopping-session-start-current-snapshot.md
- 06-shopping-session-cancel-expire.md

