Status: ready-for-agent

# Criar Purchase History a partir da finalizacao

## What to build

Ao finalizar uma Shopping Session, criar Purchase History imutavel e expor endpoints para listar, detalhar e excluir historico do usuario sem depender do Template original continuar existindo.

## API contract

- Finalizacao cria Purchase History com `template_name`, `finished_at`, `total`, `sectors_summary`.
- `GET /api/purchase-histories` lista compras do usuario ordenadas por `finished_at desc`.
- `GET /api/purchase-histories/{purchaseHistory}` retorna detalhes com sectors, items, precos, quantidades, subtotais e total.
- `DELETE /api/purchase-histories/{purchaseHistory}` remove historico proprio.

## Acceptance criteria

- [ ] Purchase History pertence obrigatoriamente a um usuario.
- [ ] Historico preserva nome do Template e resumo mesmo se Template for deletado depois.
- [ ] Listagem nao expoe historico de outros usuarios.
- [ ] Detalhe inclui informacao suficiente para tela de resumo final e revisao futura.
- [ ] Exclusao remove apenas o historico escolhido do usuario.
- [ ] Feature tests cobrem criacao via finish, listagem, detalhe, delete e preservacao apos delete do Template.

## Blocked by

- 07-shopping-session-finish-items.md

