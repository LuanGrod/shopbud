Status: accepted

# Consolidar contrato base de autenticacao e usuario

## What to build

Consolidar o contrato backend de autenticacao do MVP para que visitantes possam registrar conta, usuarios possam entrar, consultar o usuario autenticado e encerrar o acesso do token atual. O comportamento deve ser totalmente consumivel por frontend via JSON e protegido por Sanctum onde aplicavel.

## API contract

- `POST /api/auth/register` cria usuario com `name`, `email`, `password`, `password_confirmation` e retorna usuario autenticado e token.
- `POST /api/auth/login` autentica com `email` e `password` e retorna token.
- `POST /api/auth/logout` invalida o token atual.
- `GET /api/user` retorna o usuario autenticado.
- Erros de validacao retornam `422` com campos previsiveis; credenciais invalidas retornam `401`.

## Acceptance criteria

- [x] Visitante consegue registrar conta com nome, email e senha validos.
- [x] Visitante consegue fazer login com email e senha validos.
- [x] Usuario autenticado consegue fazer logout e o token atual deixa de acessar rotas protegidas.
- [x] Rotas protegidas retornam `401` sem token valido.
- [x] Contratos JSON de sucesso e erro estao cobertos por feature tests.
- [x] Rate limiting existente para login e rotas globais continua aplicado.

## Blocked by

None - can start immediately

## Comments

- Implementado backend-only em TDD. Logout agora passa por `auth:sanctum` e revoga o token autenticado atual; `/api/user` autenticado e sem token ficou coberto por feature tests.
- Verificacao final: `docker compose exec backend vendor/bin/pint --dirty --format agent` passou; `docker compose exec backend php artisan test --compact tests/Feature/AuthControllerTest.php` passou com 19 testes e 54 assertions.
- Revisado e aceito pelo humano em 2026-06-06.
