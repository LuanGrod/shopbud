Status: ready-for-agent

# Compartilhar Template por Snapshot temporario

## What to build

Permitir que o usuario gere um codigo temporario para compartilhar um Snapshot do Template. O codigo deve expirar em 24 horas e deve ser revogado quando o Template original for deletado.

## API contract

- `POST /api/templates/{template}/share` cria Shared Template para Template proprio.
- Resposta inclui `code`, `expires_at` e metadados minimos para exibicao.
- Snapshot do Shared Template contem nome do Template, Sectors ordenados e Products por criacao.
- Deletar Template remove ou invalida Shared Templates ativos vinculados.

## Acceptance criteria

- [ ] Usuario so compartilha Template proprio.
- [ ] Codigo e unico, nao previsivel e suficiente para importacao posterior.
- [ ] Shared Template expira 24 horas apos criacao.
- [ ] Snapshot nao muda quando Template original e editado depois.
- [ ] Deletar Template original revoga codigos ativos.
- [ ] Feature tests cobrem criacao, snapshot, expiracao e revogacao por delete.

## Blocked by

- 04-products-api-contract.md

