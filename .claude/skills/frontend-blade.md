---
name: frontend-blade
description: Use ao criar ou refatorar telas Blade, componentes de UI, CSS ou JS do projeto. Aciona em tarefas envolvendo views, layouts, theming dark/light, ou pós-processamento de output do Stitch.
---

# Frontend Vanilla + Blade — arquitetura do projeto

Skill completa: `read_file Skills/dev/skill-front.md` no MCP `obsidian-brain-mcp`.

## Regra de Ouro — Separação trilateral por tela

Para **cada tela principal** (ex: login, dashboard):

- `login.blade.php` — markup bruto + diretivas de layout.
- `login.css` — visuais exclusivos daquela tela.
- `login.js` — validações e manipulação DOM isoladas.

**Proibido:** `<style>` inline ou `<script>` poluidor dentro de Blade (exceto componente micro renderizado dinamicamente).

**Injeção:** via `@push('styles')` / `@push('scripts')` ou `@vite(['resources/css/pages/login.css', 'resources/js/pages/login.js'])`.

## Fundações globais

- **`global.css`** — somente reset, fontes base e CSS Custom Properties em `:root` (`--primary-color`, `--bg-color`, etc.). Toda mudança visual ampla acontece aqui.
- **`app.js`** — orquestrador geral. Setup CSRF, listeners globais. NADA de lógica de tela.

## Dark / Light obrigatório

- Módulo central: `theme-manager.js`.
- CSS de tema chaveado por `@media (prefers-color-scheme: dark)` E por atributo raiz `<html data-theme="dark">` (permite toggle do usuário).
- Persistência em `localStorage` para evitar flicker entre rotas.

## Quando usar Stitch MCP

1. Mapeie escolhas do Stitch via `apply_design_system` para `global.css`.
2. Desmembre HTML/CSS gerado conforme a Regra de Ouro.
3. Converta blocos repetidos (botão, input com floating label) em components anônimos Blade (`<x-button>`, `<x-input>`).

Stitch = wireframe rápido. Código final = padrão Laravel/Blade nosso.
