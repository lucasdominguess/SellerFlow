---
name: security-review
description: Use ao revisar código PHP/Laravel, especialmente Controllers e Services, ou quando o usuário pedir auditoria de segurança, code review, ou houver discussão de mudança em código legado. Aplica discordância ativa e checks de OWASP em Laravel.
---

# Revisão de segurança e discordância ativa

Skill completa: `read_file Skills/dev/skill-secur.md` no MCP `obsidian-brain-mcp`.

## Discordância ativa

Não concorde cegamente. Se o pedido fere arquitetura ou segurança, diga: *"Isso é uma má prática porque..."* e proponha o caminho correto.

Ao entregar código "quick & dirty", **obrigatoriamente** mostre também a versão arquitetural ideal.

## Auditoria preventiva (OWASP em Laravel)

A cada bloco de código revisado, audite:

- **Mass Assignment** — uso indevido de `$fillable` / `$guarded`. Atenção em `Model::create($request->all())`.
- **IDOR** — Controllers que atualizam Model sem verificar se o ID pertence ao tenant/usuário logado.
- **SQL Injection** — `DB::raw()`, `whereRaw()`, `selectRaw()` com input do usuário.
- **XSS** — retornos HTML/JSON com dados de usuário não filtrados.
- **Config Linux/Server** — chaves frouxas, permissões muito abertas em produção, dados de `.env` expostos no repo.

## Refatoração de legado

- Não reescreva inteiro de uma vez (a menos que pedido).
- Refatoração **incremental**: abstrações pontuais sem quebrar contratos legados sem testes.
- Padrão recorrente (eager loading esquecido 2+ vezes, regra no Controller 2+ vezes): emita alerta `Padrão identificado: [descrição]` (sem emoji em chat) e oriente a correção sistemática.
