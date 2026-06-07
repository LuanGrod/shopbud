Status: accepted

# Cancelar e expirar Shopping Session ativa

## What to build

Permitir que o usuario cancele uma Shopping Session ativa e garantir que sessoes ativas com mais de 24 horas sejam canceladas automaticamente para nao bloquear novas compras.

## API contract

- `POST /api/shopping-sessions/{shoppingSession}/cancel` altera sessao ativa propria para `cancelled`.
- Start/current tratam sessoes expiradas como canceladas antes de decidir resposta.
- Sessao expirada ou cancelada nao pode voltar para `active`.

## Acceptance criteria

- [x] Usuario consegue cancelar apenas sua propria Shopping Session ativa.
- [x] Cancelar sessao ja finalizada, cancelada ou alheia retorna erro apropriado.
- [x] Sessao ativa expirada ha mais de 24 horas e marcada como `cancelled`.
- [x] Iniciar nova sessao apos expiracao cria nova sessao quando nao houver ativa valida.
- [x] Expiracao usa campo persistido de `expires_at` ou equivalente explicito no schema.
- [x] Feature/unit tests cobrem cancelamento, expiracao e bloqueios de status.

## Blocked by

- 05-shopping-session-start-current-snapshot.md

