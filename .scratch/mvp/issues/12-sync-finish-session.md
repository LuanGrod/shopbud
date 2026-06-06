Status: ready-for-agent

# Sincronizar finalizacao offline pendente

## What to build

Expor endpoint de Sync para aceitar operacoes offline de alto nivel, especialmente `finish_session`, permitindo que o frontend sincronize uma compra finalizada offline quando a conexao voltar.

## API contract

- `POST /api/sync` recebe `operations[]`.
- Operacao `finish_session` contem identificador local opcional, `session_id` e payload final equivalente ao endpoint de finish.
- Resposta informa operacoes aplicadas e rejeitadas individualmente, com motivo estavel para rejeicoes.
- Finalizacao atrasada usa as mesmas validacoes e calculos oficiais do finish online.

## Acceptance criteria

- [ ] Sync exige usuario autenticado.
- [ ] `finish_session` de sessao ativa nao expirada e aplicado com mesmo resultado do finish online.
- [ ] `finish_session` de sessao expirada, cancelada, finalizada ou alheia e rejeitado sem criar historico.
- [ ] Resposta permite ao frontend reconciliar operacoes pendentes no IndexedDB.
- [ ] Operacao aplicada e idempotente o suficiente para evitar duplicar historico em retry documentado pelo contrato.
- [ ] Feature tests cobrem sucesso apos reconexao, rejeicao por expiracao/cancelamento e isolamento por usuario.

## Blocked by

- 07-shopping-session-finish-items.md
- 08-purchase-history-api-contract.md
