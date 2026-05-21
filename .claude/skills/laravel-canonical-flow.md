---
name: laravel-canonical-flow
description: Use sempre que o usuário criar, refatorar ou revisar qualquer fluxo backend Laravel envolvendo Controller, FormRequest, Service, Repository, DTO, ResponseDTO ou endpoint REST. Garante que o código siga o fluxo canônico do projeto SellerFlow.
---

# Fluxo canônico Laravel — âncora do projeto

Padrão detalhado completo: `read_file Skills/dev/skill-layers.md` no MCP `obsidian-brain-mcp` antes de gerar código.

## Regras-âncora (sumário operacional)

**Fluxo obrigatório:**
```
HTTP → FormRequest → CommandDTO → Service → Repository → Eloquent → ResponseDTO → JSON
```

**Camadas (responsabilidade única):**
- **Controller** — fino. Recebe `FormRequest`, instancia `CommandDTO::fromRequest($request->validated())`, chama Service, devolve `JsonResponse`. Sem lógica de negócio.
- **FormRequest** — valida payload. Nunca passe `$request` cru ao Service — sempre `$request->validated()`.
- **CommandDTO** — `readonly`, tipado. `fromRequest()` + `toArray()` com `array_filter` (permite updates parciais).
- **Service** — orquestra regras. Recebe `CommandDTO`, devolve `ResponseDTO`. Nunca faz query. `DB::transaction()` quando há múltiplas escritas.
- **Repository** — único ponto de query. Retorna Eloquent Model. `create()`/`update()` chamam `->load($this->withRelations())` antes de retornar. Centraliza relações em `withRelations(): array` privado.
- **ResponseDTO** — contrato público da API. `fromModel()`. `toArray()` SEM `array_filter` (campos nullable aparecem como `null`).

## Antipadrões — sinalizar SEMPRE

- Service retorna Eloquent Model cru → vaza estrutura interna.
- `store()` retorna Model sem `->load()` → frontend recebe resposta incompleta.
- `$request` passado ao Service → acopla HTTP ao domínio.
- `array_filter` agressivo no ResponseDTO → campos nullable somem.
- Lógica de negócio no Controller → impossibilita teste unitário.
- Query direta no Service → quebra SRP, rompe testabilidade.

## Geradores custom do projeto

Sempre prefira:
```bash
php artisan make:dto {Name}
php artisan make:service {Name}
```

## Convenções

- Código em **inglês**, comentários em **PT-BR**.
- Sem PHPDoc descritivo (`/** @param... */`) em métodos internos.
- API versionada (`/api/v1`).
- Interface do Repository registrada no `AppServiceProvider`.
