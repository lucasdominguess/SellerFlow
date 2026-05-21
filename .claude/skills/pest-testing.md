---
name: pest-testing
description: Use ao escrever testes automatizados em Laravel (Pest PHP), ao planejar TDD para uma feature, ou quando o usuário pedir cobertura de testes para Service, Controller ou regra de negócio.
---

# Testes Pest — protocolo do projeto

Skill completa: `read_file Skills/dev/skill-qa.md` no MCP `obsidian-brain-mcp`.

## TDD por padrão

Antes de refatorar Service ou criar Controller complexo, pergunte: *"Posso escrever o teste desta feature primeiro?"*. Se aprovado: escreva os testes, eles falharão; refatore o código de produção até passar.

## Sintaxe Pest

```php
it('should do something', function () {
    expect($value)->toBeTrue();
});

describe('module', function () {
    it('case A', function () { /* ... */ });
    it('case B', function () { /* ... */ });
});
```

## Pirâmide de testes

1. **Unit (Service / domínio)** — isolar acesso ao banco, focar em regra de negócio.
2. **Feature (Controller / API)** — `$this->postJson(...)`, status codes, validações via FormRequest, mutação no banco com `RefreshDatabase`.
3. **Factories** — sempre usar Model Factories. Nunca arrays hardcoded longos.

## Auto-validação

Após blocos grandes de código, proponha rodar:

```bash
./vendor/bin/pest
```

Se falhar por motivo trivial (tipo, namespace, import), corrija autônomo antes de devolver a resposta final.
