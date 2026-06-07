Status: accepted

# Iniciar e recuperar Shopping Session ativa com Snapshot

## What to build

Criar o fluxo backend para iniciar uma Shopping Session online a partir de um Template valido e recuperar a sessao ativa atual. A sessao deve capturar um Snapshot imutavel da estrutura do Template no momento de inicio.

## API contract

- `POST /api/shopping-sessions` recebe `template_id` e inicia sessao quando nao houver ativa valida.
- Se ja existir Shopping Session `active` nao expirada para o usuario, retorna essa sessao em vez de criar outra.
- `GET /api/shopping-sessions/current` retorna a sessao ativa valida ou `204`/resposta vazia padronizada quando nao houver.
- Resposta de sessao inclui `id`, `status`, `template_id`, `expires_at`, `snapshot.sectors[].products[]`.

## Acceptance criteria

- [x] Iniciar sessao exige usuario autenticado.
- [x] Iniciar sessao exige Template proprio com pelo menos um Sector.
- [x] Template sem Sectors retorna erro de dominio apropriado.
- [x] Apenas uma Shopping Session ativa valida existe por usuario.
- [x] Snapshot copia nomes e estrutura de Sectors e Products no momento do inicio.
- [x] Edicoes posteriores no Template nao alteram Snapshot da sessao.
- [x] Products adicionados durante a sessao nao sao persistidos no Template por este fluxo.
- [x] Feature tests cobrem ownership, sessao existente, Snapshot e independencia.

## Blocked by

- 04-products-api-contract.md
