# Projeto: SellerFlow

**Stack:** Laravel 11, PHP 8.3+, Postgres, Redis. Backend API REST.
**Frontend:** apartado — SPA em Vue.js consumindo a API (repositório separado).
**Tipo:** API REST. Ferramenta interna de gestão para seller de marketplaces (foco Shopee).
**Status:** MVP em desenvolvimento — escopo em `Plans/plan-sellerflow-sistema.md` (no Obsidian Brain).

---

## Obsidian Brain (memória técnica externa)

Este projeto consulta o Brain global para padrões arquiteturais reutilizáveis.

- **Caminho local do Brain:** `C:\Users\lukas\git_projetos\Outros\Obsidian-ld\Obsidian-LD`
- **MCP server:** `obsidian-brain-mcp` v1.1 (tools: `brain_status`, `read_file`, `read_section`, `search_brain`)
- **Como usar:** prefira `read_section` quando souber a skill **e a seção**; `read_file` quando precisar do arquivo inteiro; `search_brain` só para descoberta; `brain_status` só na 1ª conversa em máquina nova.

### Quando consultar o Brain (regra mecânica)

> `Skills/ops/skill-core.md` é o índice operacional do Brain — leia-o primeiro em caso de dúvida sobre qual skill usar.

| Tarefa                                                    | Skill primária                            |
| --------------------------------------------------------- | ------------------------------------------ |
| Criar/refatorar fluxo backend (Controller→Service→Repo) | `Skills/dev/skill-layers.md`             |
| Padrões de engenheiro backend sênior (PHP/Laravel)      | `Skills/dev/skill-back.md`               |
| Preencher DTOs/FormRequests do `make:crud` via schema     | `Skills/dev/skill-dto-filler.md`         |
| Revisão de segurança / discordância ativa              | `Skills/dev/skill-secur.md`              |
| Escrever testes (Pest)                                    | `Skills/dev/skill-qa.md`                 |
| Testes unitários/feature por camada                      | `Skills/dev/skill-unit-tests.md`         |
| Tela/UI nova (frontend Vue apartado)                      | `Skills/dev/skill-front.md`              |
| Setup de infra / deploy / CI/CD                           | `Skills/dev/skill-infra.md`              |
| Documentar API (Swagger/OpenAPI)                          | `Skills/dev/skill-swagger-docs.md`       |
| Gerar/atualizar collection no Postman                     | `Skills/dev/skill-postman-crud.md`       |
| Banco de dados via Supabase (MCP)                         | `Skills/dev/skill-supabase.md`           |
| Tarefa complexa (>3 arquivos, planejar antes de codar)    | `Skills/ops/skill-planner.md`            |
| Decisão arquitetural durável                            | `Skills/ops/skill-memory.md` (criar ADR) |
| Modo aprendizado / mentoria técnica                      | `Skills/ops/skill-mentor.md`             |
| Setup/configuração de MCPs                               | `Skills/ops/mcp-setup.md`                |

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
- Service sempre **recebe um CommandDTO** (domínio), nunca array puro. O Controller monta `XDTO::fromRequest($request->validated())` e entrega o DTO ao Service.
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

# Geradores custom do projeto (preferir a criar arquivos manualmente)
php artisan make:crud {Name}        # stack CRUD completo + binds + rotas (aceita subpastas, ex: Business/Product)
php artisan make:service {Name}     # Service (flags: -r repo, -c contract, -C controller, -d dto, -m métodos CRUD)
php artisan make:dto {Name}         # DTO (-r ResponseDTO, -A fromArray, --all gera o par completo)
php artisan postman:generate        # gera/atualiza a collection do Postman
php artisan stock:rebuild-balances  # recalcula stock_balances a partir de stock_movements
php artisan cls                     # limpa caches (--all inclui Composer, --prod otimiza p/ produção)
```
