# Prompt para Stitch — Tela de Estoque + Reorganização do Menu

> Cole este bloco no agente que tem acesso ao Google Stitch. Ele cobre duas mudanças num único pedido.

---

## CONTEXTO

Você já gerou 4 telas do sistema **TradeFlow** (SellerFlow): Dashboard, Nova Compra, Nova Venda e Fluxo de Caixa. Mantenha **exatamente** o mesmo estilo visual: dark mode, paleta indigo/verde/rosa/âmbar, sidebar 240px com fundo `#0f172a`, cards com fundo `#1e293b`, bordas `#334155`, cantos arredondados 8px, ícones outline (Lucide), tipografia sans-serif.

Duas tarefas:

1. **Reorganizar a sidebar** em categorias com cabeçalhos (como já existe parcialmente na tela "Nova Venda" com `FINANCEIRO` e `CADASTROS`).
2. **Criar a tela "Estoque — Saldo Atual"**, que ainda não foi gerada.

---

## TAREFA 1 — Reorganização do menu lateral (hamburger)

Padronize o menu em **5 grupos** com label maiúsculo, espaçamento 14px entre grupos. Use exatamente esta ordem e nomenclatura. **Aplique a sidebar idêntica em TODAS as telas do sistema** — Dashboard, Compras, Vendas, Estoque, Financeiro e Cadastros.

```
[Logo TradeFlow / SellerFlow]

OPERAÇÃO
  ├─ 🗂  Dashboard
  ├─ 🛒  Compras
  └─ 🏷  Vendas

ESTOQUE
  ├─ 📦  Saldo
  ├─ 🔄  Movimentações
  └─ 🎚  Ajustes

FINANCEIRO
  ├─ 💵  Contas a Pagar
  ├─ 🧾  Contas a Receber
  └─ 📈  Fluxo de Caixa

CADASTROS
  ├─ 📦  Produtos
  ├─ 🏭  Fornecedores
  ├─ 🏷  Categorias
  ├─ 💳  Formas de Pagamento
  └─ 🏬  Marketplaces

SISTEMA
  ├─ 👥  Usuários
  ├─ 🛡  Perfis
  └─ 🏪  Loja

[footer fixo no fim]
  └─ ⎋  Sair
```

**Regras visuais do menu:**
- Cabeçalho do grupo: font-size 11px, cor `#64748b`, letter-spacing 0.08em, uppercase, padding 12px 16px 6px.
- Item: font-size 14px, cor `#cbd5e1`, padding 10px 16px, border-radius 6px.
- Item ativo: fundo `#1e293b`, cor `#a5b4fc` (indigo claro), barra vertical de 3px indigo na esquerda.
- Hover: fundo `#1e293b` com 50% opacidade.
- Comportamento hamburger: já implementado — manter. Quando colapsado, mostrar apenas os ícones (largura 64px). Cabeçalhos dos grupos somem.

---

## TAREFA 2 — Nova tela: "Estoque — Saldo Atual"

Rota conceitual: `Estoque > Saldo`. Sidebar com "Saldo" ativo.

### Estrutura geral

Aproveite o mesmo shell das telas existentes (sidebar + topbar com busca, notificações, ajuda, configurações, avatar).

### Header da página

- **Título:** `Saldo de Estoque`
- **Subtítulo:** `Posição atual calculada a partir de todas as movimentações registradas.`
- **Ações à direita:**
  - Botão secundário (outline): `Ajuste manual` (ícone 🎚)
  - Botão primário (indigo): `Exportar` (ícone de download)

### Bloco de KPIs (4 cards, igual ao Fluxo de Caixa)

| Card | Valor exemplo | Cor do destaque |
|---|---|---|
| **SKUs ATIVOS** | 248 | indigo `#a5b4fc` |
| **UNIDADES EM ESTOQUE** | 12.847 | branco `#f8fafc` |
| **PRODUTOS EM ALERTA** | 7 | âmbar `#f59e0b` (ícone ⚠) |
| **PRODUTOS ZERADOS** | 3 | rosa `#ec4899` (ícone ⊘) |

Cada card: padding 24px, label uppercase 11px muted, valor em 28px bold, ícone à direita.

### Barra de filtros (acima da tabela)

Linha única, fundo do card `#1e293b`, padding 16px:

1. **Busca** (campo grande, ícone lupa): placeholder `Buscar por SKU, nome ou código de barras…`
2. **Categoria** (select): `Todas as categorias`
3. **Status** (select): `Todos`, `Em estoque`, `Estoque baixo`, `Zerado`
4. **Toggle:** `Só com movimentação nos últimos 30 dias` (alinhado à direita)

### Tabela principal — "Produtos em estoque"

Card único, header com título e contador (`248 produtos`). Colunas:

| Coluna | Largura | Conteúdo de exemplo |
|---|---|---|
| **SKU** | 10% | `FON-XY200-PRT` (mono, cor muted) |
| **Produto** | 28% | `Fone de Ouvido Bluetooth XY-200` (cor `#e2e8f0`, 14px) — linha 2 com categoria em chip pequeno |
| **Categoria** | 12% | chip `Eletrônicos` (fundo `#1e293b`, border `#334155`) |
| **Saldo Atual** | 10% | `42` un. (bold, alinhado à direita) |
| **Mínimo** | 8% | `10` un. (muted) |
| **Status** | 12% | chip colorido (ver abaixo) |
| **Última movimentação** | 14% | `Há 3 dias` + tooltip com data exata |
| **Ações** | 6% | botão ghost `Ver histórico` (ícone ⟶) |

**Chips de status:**
- `Em estoque` — fundo `#10b98120`, texto `#34d399` (verde).
- `Estoque baixo` — fundo `#f59e0b20`, texto `#fbbf24` (âmbar).
- `Zerado` — fundo `#ec489920`, texto `#f472b6` (rosa).

**Linhas de exemplo (preencher mínimo 6):**

1. `FON-XY200-PRT` · Fone de Ouvido Bluetooth XY-200 · Eletrônicos · **42** · 10 · Em estoque · Há 3 dias
2. `CAB-USB-2M` · Cabo USB-C Turbo 2M · Acessórios · **128** · 50 · Em estoque · Há 1 dia
3. `CAR-20W-BR` · Carregador Rápido 20W · Acessórios · **8** · 15 · **Estoque baixo** · Há 6 horas
4. `CAP-IP15-AZ` · Capa iPhone 15 Azul · Capas · **0** · 5 · **Zerado** · Há 2 semanas
5. `PEL-IP15-VD` · Película 3D iPhone 15 · Capas · **3** · 20 · **Estoque baixo** · Hoje
6. `SUP-VEI-MAG` · Suporte Veicular Magnético · Acessórios · **64** · 10 · Em estoque · Há 5 dias

Paginação no rodapé do card: `Mostrando 1–10 de 248` à esquerda; controles `< 1 2 3 … >` à direita.

### Detalhes de interação

- Linha inteira é hover-clicável (fundo `#1e293b50`).
- Clicar em "Ver histórico" leva à tela `Estoque > Movimentações` com filtro pré-aplicado por aquele produto.
- O botão "Ajuste manual" abre **modal** com campos: produto (autocomplete), tipo (entrada/saída), quantidade, motivo (textarea). Botões "Cancelar" e "Confirmar ajuste".
- Skeleton loader nas linhas durante carregamento (não spinner).

---

## ENTREGÁVEL ESPERADO

1. Sidebar reorganizada conforme **Tarefa 1**, aplicada em todas as telas anteriores e na nova.
2. Tela `Estoque — Saldo Atual` completa conforme **Tarefa 2**, com KPIs + filtros + tabela populada.
3. Modal de "Ajuste manual" como variação acessória.

Mantenha consistência absoluta com as telas já geradas — mesmas cores, mesmos paddings, mesma tipografia, mesmo padrão de chips e botões.
