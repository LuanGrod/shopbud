Status: ready-for-agent

# Importar Template a partir de Shared Template valido

## What to build

Permitir que usuario autenticado importe um Template independente a partir de codigo de Shared Template valido. A importacao deve copiar a estrutura do Snapshot e resolver conflito de nome sem depender do Template original.

## API contract

- `POST /api/shared-templates/import` recebe `code`.
- Codigo valido e nao expirado cria novo Template para o usuario autenticado com Sectors e Products do Snapshot.
- Se o usuario ja possuir Template com mesmo nome, resposta cria com nome sugerido/ajustado conforme regra definida no backend.
- Resposta retorna o Template importado no contrato de detalhe estrutural.

## Acceptance criteria

- [ ] Importacao exige usuario autenticado.
- [ ] Codigo inexistente, expirado ou revogado retorna erro apropriado.
- [ ] Template importado pertence ao usuario importador.
- [ ] Estrutura importada e independente do dono original e do Shared Template.
- [ ] Conflito de nome e resolvido de forma previsivel e testada.
- [ ] Usuario pode importar codigo proprio se nao houver decisao contraria no PRD.
- [ ] Feature tests cobrem sucesso, expiracao/revogacao, conflito de nome e independencia.

## Blocked by

- 10-shared-template-create-revoke.md

