# Projeto: SellerFlow

**Stack:** Laravel 11, PHP 8.2+, MariaDB, Redis, Docker (multi-stage), Vanilla JS + Blade, Vite.
**Tipo:** API REST + frontend Blade. Ferramenta interna de gestão para seller de marketplaces (foco Shopee).
**Status:** MVP em desenvolvimento — escopo em `Plans/plan-sellerflow-sistema.md` (no Obsidian Brain).

---

## Obsidian Brain (memória técnica externa)

Este projeto consulta o Brain global para padrões arquiteturais reutilizáveis.

- **Caminho local do Brain:** `C:\Users\lukas\git_projetos\Outros\Obsidian-ld\Obsidian-LD`
- **MCP server:** `obsidian-brain-mcp` v1.1 (tools: `brain_status`, `read_file`, `read_section`, `search_brain`)
- **Como usar:** prefira `read_section` quando souber a skill **e a seção**; `read_file` quando precisar do arquivo inteiro; `search_brain` só para descoberta; `brain_status` só na 1ª conversa em máquina nova.

### Quando consultar o Brain (regra mecânica)

| Tarefa                                                  | Skill primária                  |
|---------------------------------------------------------|----------------------------------|
| Criar/refatorar fluxo backend (Controller→Service→Repo) | `Skills/dev/skill-layers.md`         |
| Revisão de segurança / discordância ativa               | `Skills/dev/skill-secur.md`      |
| Escrever testes (Pest)                                  | `Skills/dev/skill-qa.md`         |
| Tela/UI nova (Blade + CSS + JS isolados)                | `Skills/dev/skill-front.md`      |
| Setup Docker, deploy, CI/CD                             | `Skills/dev/skill-infra.md`      |
| Documentar API (Swagger/OpenAPI)                        | `Skills/dev/skill-swagger-docs.md` |
| Tarefa complexa (>3 arquivos, planejar antes de codar)  | `Skills/ops/skill-planner.md`    |
| Decisão arquitetural durável                            | `Skills/ops/skill-memory.md` (criar ADR) |

### Quando NÃO consultar o Brain

- Rename de variável/função
- Fix de typo / formatação
- Comando rápido (ex: "rode os testes", "abra o arquivo X")
- Pergunta puramente informativa sobre o código existente
- Qualquer tarefa mecânica que não envolva decisão arquitetural

---

## Padrões inegociáveis deste projeto

- **Fluxo canônico:** `FormRequest → CommandDTO → Service → Repository → ResponseDTO`. Detalhe em `Skills/dev/skill-layers.md`.
- Nunca passar `$request` cru ao Service — sempre `$request->validated()`.
- Service nunca retorna Eloquent Model — sempre `ResponseDTO::fromModel()`.
- Repository é o único que faz query. `create()` e `update()` chamam `->load($this->withRelations())` antes de retornar.
- Interface do Repository declarada e vinculada no `AppServiceProvider`.
- `update()` em Service com múltiplas escritas usa `DB::transaction()`.
- Código em **inglês** (classes, métodos, variáveis, migrates); comentários e explicações em **PT-BR**.
- Sem PHPDoc descritivo em métodos internos. Comentários só quando o "porquê" não for óbvio.
- API versionada (`/api/v1`), respostas unificadas em JSON via `ApiResponse::success($dto)`.

---

## Comandos úteis

```bash
# Backend
php artisan serve
php artisan migrate:fresh --seed
./vendor/bin/pest

# Frontend / assets
npm run dev
npm run build

# Docker
docker compose up -d
docker compose logs -f app

# Geradores custom do projeto
php artisan make:dto {Name}
php artisan make:service {Name}
php artisan postman:generate
```

---

## Notas específicas deste projeto

- **Sessão:** driver padrão é `cookie` (commit `0f8d016`). Não voltar para `file` — containers Render são stateless.
- **Custom commands:** `app/Console/Commands/` tem `MakeDTO`, `MakeService`, `PostmanGenerate`, `CleanAll`. Preferir esses geradores a criar arquivos manualmente.
- **Exception handler:** `CustomException.php` (não `CustomExcepiton.php` — typo do legado já corrigido). Registrado em `bootstrap/app.php`.
- **Shopee integração:** **fora do MVP**. Vendas/compras entram manualmente. Não sugerir integração com a API da Shopee nesta fase.
- **Multi-loja:** modelar com tabela própria, **mas não implementar UI/lógica multi-loja**. Uma loja só no MVP.
- **Custo de produto / margem:** não calcular nesta versão. Ignorar campos de custo médio.
