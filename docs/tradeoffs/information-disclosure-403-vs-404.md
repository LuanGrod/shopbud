# Information Disclosure: 403 vs 404

## O Que É

Information Disclosure é uma categoria de vulnerabilidade onde a aplicação revela informações que podem ser úteis para atacantes. No contexto de APIs, um caso comum é a diferença entre responder `403 Forbidden` ou `404 Not Found` quando um usuário tenta acessar um recurso que existe mas não lhe pertence.

## O Problema da Enumeração

Quando uma API usa IDs sequenciais (auto-increment: 1, 2, 3...), um atacante pode tentar enumerar os IDs para descobrir quantos recursos existem e quem são os proprietários.

**Exemplo com 403:**
```bash
GET /api/templates/1  → 403 Forbidden  # existe, mas é de outro usuário
GET /api/templates/2  → 403 Forbidden  # existe, mas é de outro usuário
GET /api/templates/3  → 200 OK         # existe e é meu!
```

O atacante aprendeu: existem pelo menos 3 templates, e o #3 pertence ao usuário alvo.

**Exemplo com 404:**
```bash
GET /api/templates/1  → 404 Not Found
GET /api/templates/2  → 404 Not Found
GET /api/templates/3  → 200 OK
```

O atacante não sabe se os IDs 1 e 2 não existem ou se existem mas não estão acessíveis.

## Como Está Atualmente

No Shopbud, ao acessar um template de outro usuário:

```php
// TemplateController.php
public function show(Template $template)
{
    $this->authorize('view', $template);  // Policy retorna false se user_id != auth()->id()
    return new TemplateResource($template);
}
```

Quando a Policy retorna `false`, o Laravel lança `AuthorizationException` que resulta em:

```json
HTTP 403 Forbidden
{
  "message": "This action is unauthorized."
}
```

## Por Que Isso Não É Crítico

### 1. A Indústria Usa 403

Grandes empresas retornam 403 para authorization:
- GitHub API
- Stripe API
- AWS APIs
- Google APIs

Se fosse um problema de segurança crítico, essas empresas teriam corrigido.

### 2. Outros Vetores de Enumeração

Mesmo retornando 404, a enumeração ainda é possível através de:
- **Timing attacks**: Diferença de tempo entre 404 rápido (não existe) vs query + 403 (existe mas negado)
- **Criação de recursos**: Criar um template e ver o ID gerado
- **Outros endpoints**: Endpoints que listam resources vazam informações

### 3. Proteção Real é Rate Limiting

A defesa real contra enumeration attack é bloquear tentativas repetidas:

```php
// api.php
Route::middleware(['throttle:global', 'auth:sanctum'])
```

O throttle `global` já está configurado e bloqueia tentativas em massa.

### 4. IDs Sequenciais São O Problema Real

Se o banco usa auto-increment (1, 2, 3...), a enumeration é trivial independente de 403/404. Soluções:
- **UUIDs**: Previnem enumeration por design
- **ULIDs**: UUIDs ordenados temporalmente
- **Hash IDs**: IDs encriptados/public-facing

Mas isso é uma mudança arquitetural maior com tradeoffs próprios.

## Prioridade de Segurança

| Prioridade | Proteção | Status |
|-----------|----------|--------|
| 🔴 Alta | Autenticação forte | ✅ Sanctum |
| 🔴 Alta | Autorização por Policy | ✅ Implementado |
| 🔴 Alta | Rate Limiting | ✅ throttle:global |
| 🟡 Média | Input Validation | ✅ Form Requests |
| 🟡 Média | SQL Injection Protection | ✅ Eloquent |
| 🟢 Baixa | 403 vs 404 (Information Disclosure) | ⚠️ Atualmente 403 |

## Como Mudar Para 404 (Opcional)

Se ainda assim quiser retornar 404, a maneira correta é customizar o handler de exceção:

```php
// app/Exceptions/Handler.php
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

public function render($request, Throwable $e)
{
    if ($e instanceof AuthorizationException) {
        return response()->json(['message' => 'Resource not found'], 404);
    }

    return parent::render($request, $e);
}
```

Isso converte todos os erros de autorização em 404, consistente em toda a aplicação.

## Decisão

**Não implementado por enquanto.**

O 403 atual é aceitável e segue convenções da indústria. A energia é melhor gasta em:
- Testes
- Features
- Outras áreas de segurança mais críticas

Se no futuro isso se tornar uma preocupação real (ex: requisito de compliance), a mudança é simples.

## Referências

- [OWASP Information Disclosure](https://owasp.org/www-community/attacks/Information_disclosure)
- [Laravel Authorization](https://laravel.com/docs/authorization)
- [Web Security Academy - IDOR](https://portswigger.net/web-security/insecure-direct-object-references)
