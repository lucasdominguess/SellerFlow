# Briefing para Google Stitch — TradeFlow (SellerFlow)

> Documento estruturado para o agente de I.A. com acesso ao Google Stitch gerar os esboços de UI do sistema. Cada tela tem um bloco autocontido — o agente pode rodar tudo em sequência ou tela por tela.

---

## 0. Contexto do produto

- **Nome do sistema:** TradeFlow (codinome interno: SellerFlow).
- **Usuário-alvo:** pequeno seller de marketplaces (Shopee como primeiro alvo). Roda a operação sozinho ou com 1–2 funcionários (estoque + financeiro).
- **Problema que resolve:** substitui as planilhas. Responde 3 perguntas: *o que eu tenho?*, *o que vou receber/pagar?*, *quanto sobra?*.
- **Idioma da UI:** **Português brasileiro**. Toda label, botão, placeholder e mensagem em PT-BR.
- **Plataforma:** web responsivo (desktop-first; mobile usável mas não otimizado no MVP).
- **Não fazer no MVP:** integração Shopee, custo médio/margem, multi-loja em UI, app mobile, gráficos avançados.

---

## 1. Diretrizes visuais

| Atributo | Decisão |
|---|---|
| Estilo | Clean, funcional, "dashboard de operação" (não SaaS marketing). Densidade alta de informação. |
| Paleta | Dark mode primário. Fundo `#0f1117`, superfícies `#1e293b`, bordas `#334155`. Texto `#e2e8f0` / muted `#94a3b8`. |
| Acento | Indigo `#6366f1` (primário), verde `#10b981` (sucesso/entrada), rosa `#ec4899` (financeiro/saída), âmbar `#f59e0b` (atenção/pendente). |
| Tipografia | Sans-serif neutro (Inter, Segoe UI, system-ui). Pesos 400/500/600/700. |
| Iconografia | Lucide ou Phosphor — outline, 1.5px stroke. |
| Cantos | Border-radius 8px em cards, 6px em inputs, 999px em chips/badges. |
| Densidade | Tabelas com 14px font, padding 8–12px. Cards com padding 20–24px. |
| Componentes-padrão | Sidebar fixa à esquerda, topbar com busca e perfil, conteúdo principal em cards. Modais para formulários médios; página dedicada para formulários grandes (compra/venda). |

---

## 2. Layout global (shell)

Aplica a todas as telas internas (pós-login):

```
┌─────────┬───────────────────────────────────────────────┐
│         │  [busca]                       [🔔] [avatar]  │
│         ├───────────────────────────────────────────────┤
│ SIDEBAR │                                               │
│         │              CONTEÚDO DA TELA                 │
│         │                                               │
└─────────┴───────────────────────────────────────────────┘
```

**Sidebar (240px, fundo `#0f172a`):** logo TradeFlow no topo, depois grupos:

1. **Operação** — Dashboard · Compras · Vendas
2. **Estoque** — Saldo · Movimentações · Ajustes
3. **Financeiro** — Contas a Pagar · Contas a Receber · Fluxo de Caixa
4. **Cadastros** — Produtos · Fornecedores · Categorias · Formas de Pagamento · Marketplaces
5. **Sistema** — Usuários · Perfis · Loja

Footer da sidebar: avatar + nome + role + botão sair.

---

## 3. Telas (em ordem de prioridade)

### 3.1. Login

- Centralizado, card único 400px de largura.
- Logo TradeFlow no topo.
- Campos: email, senha.
- Botão "Entrar" (full-width, indigo).
- Link discreto "Esqueci minha senha".
- Sem ilustração extra — clean.

### 3.2. Dashboard (home)

Grid 12 colunas. Quatro **KPI cards** no topo (3 colunas cada):

1. **Vendas hoje** — valor em R$ + qtd de pedidos + delta vs ontem.
2. **A receber esta semana** — valor + qtd de contas.
3. **A pagar esta semana** — valor + qtd (destaque âmbar se houver vencidas).
4. **Saldo previsto fim do mês** — valor + delta vs mês passado.

Abaixo, duas colunas:
- **Esquerda (8 cols)**: gráfico simples de fluxo de caixa (linha) — últimos 30 dias, duas séries: realizado e projetado.
- **Direita (4 cols)**: lista de **alertas** — produtos com estoque mínimo, contas vencidas, vendas sem repasse há mais de X dias.

Abaixo do gráfico: tabela compacta **"Últimas movimentações"** — 5 linhas, colunas: data, tipo (chip), descrição, valor.

### 3.3. Compras — listagem

- Header: título + botão primário "Nova compra".
- Filtros em uma linha: busca · fornecedor · período · status pagamento.
- Tabela: data, nº nota, fornecedor, qtd itens, valor total, status (chip), ações (ver/editar).
- Paginação no rodapé.

### 3.4. Compras — formulário (página dedicada, não modal)

Três seções verticais, cada uma um card:

**Card 1 — Cabeçalho**
- Fornecedor (select com busca, + botão criar inline)
- Data
- Nº da nota (opcional)

**Card 2 — Itens**
- Tabela editável: produto (autocomplete) · qtd · valor unitário · subtotal · botão remover linha.
- Botão "+ Adicionar item" no rodapé da tabela.
- Total destacado à direita.

**Card 3 — Pagamento**
- Forma de pagamento (select)
- Nº de parcelas (input numérico — só visível se forma permite)
- Preview das parcelas geradas: tabela pequena com "Parcela 1/N · vencimento · valor".

Footer fixo: botões "Cancelar" e "Salvar compra" (indigo).

> Após salvar, mostrar toast: *"Compra salva. Estoque atualizado e N contas a pagar criadas."*

### 3.5. Vendas — listagem

Estrutura igual a Compras, mas com colunas:
- data, nº pedido, marketplace (chip colorido por origem), valor bruto, valor líquido, previsão repasse, status, ações.

Filtro adicional: marketplace.

### 3.6. Vendas — formulário

Mesma estrutura de Compras, com diferenças:

**Card 1 — Cabeçalho**
- Marketplace (select)
- Data da venda
- Nº do pedido no marketplace

**Card 2 — Itens** — igual a Compras.

**Card 3 — Repasse**
- Taxa do marketplace (valor ou %)
- Frete pago/patrocinado
- Data prevista de repasse (com auto-cálculo: data + payout_days do marketplace)
- Cálculo destacado: `Bruto − Taxas − Frete = Líquido`.

> Após salvar: *"Venda registrada. Estoque debitado e conta a receber criada para DD/MM."*

### 3.7. Estoque — saldo atual

- Filtros: busca por SKU/nome, categoria, "só com estoque baixo".
- Tabela: SKU, produto, categoria, saldo atual, mínimo, status (verde / âmbar se ≤ mínimo / vermelho se 0), última movimentação, ação "ver histórico".
- Botão secundário no header: "Ajuste manual".

### 3.8. Estoque — histórico de movimentações

- Filtros: produto, tipo (compra/venda/ajuste), período.
- Tabela: data, produto, tipo (chip), origem (link para compra/venda/ajuste), qtd (verde se entrada, rosa se saída), saldo após.

### 3.9. Estoque — ajuste manual (modal)

- Produto (autocomplete)
- Tipo: entrada / saída
- Quantidade
- Motivo (textarea: perda, quebra, contagem física…)
- Botão "Confirmar ajuste".

### 3.10. Contas a Pagar — listagem

- Header: título + botão "Nova despesa".
- Tabs no topo: **Todas · Pendentes · Pagas · Vencidas** (com contador em cada).
- Filtros: período, categoria, fornecedor.
- Tabela: vencimento, descrição, fornecedor, categoria (chip), valor, status, ação principal "Marcar como pago".
- Linha de **total** no rodapé filtrado.

### 3.11. Contas a Pagar — modal "Marcar como pago"

- Data do pagamento (default = hoje)
- Forma de pagamento (select)
- Confirmar.

### 3.12. Contas a Pagar — formulário "Nova despesa" (modal grande)

- Descrição
- Categoria financeira
- Fornecedor (opcional)
- Valor
- Vencimento (ou múltiplos vencimentos se for recorrente — botão "Adicionar parcela")
- Forma de pagamento.

### 3.13. Contas a Receber — listagem

Estrutura igual a Pagar, com colunas:
- previsão recebimento, pedido (link p/ venda), marketplace, bruto, taxa, líquido, status, ação "Marcar como recebido".

> Destacar visualmente quando `data_real > data_prevista` (atraso de repasse) — ícone âmbar com tooltip "Atraso de X dias".

### 3.14. Fluxo de Caixa

- Seletor de período no topo: dia / semana / mês (mês default).
- Três KPI cards: **Saldo inicial · Entradas · Saídas · Saldo final** (4 cards).
- Gráfico de barras empilhadas (entradas verdes para cima, saídas rosas para baixo) por dia/semana/mês.
- Tabela detalhada abaixo: data, descrição, categoria, entrada, saída, saldo acumulado.
- Toggle "Incluir projeção" — quando ligado, adiciona linhas semi-transparentes com previsões.

### 3.15. Cadastros (modais simples para todos)

Padrão de modal para: Produto, Fornecedor, Categoria de produto, Categoria financeira, Forma de pagamento, Marketplace.

Cada um:
- Listagem em página própria com busca e botão "Novo".
- Modal de criação/edição com os campos do schema ER.
- Confirmação inline ao salvar.

### 3.16. Usuários e perfis

- Listagem de usuários: nome, email, perfil (chip), última atividade, ações.
- Modal de criação: nome, email, perfil (select com 3 opções), senha temporária.
- Tela de perfis (admin only): lista das 3 roles com descrição das permissões.

---

## 4. Princípios de interação

1. **Confirmação por toast curto** em todas as ações de salvar/excluir — nunca alert nativo.
2. **Loading skeletons** em tabelas, não spinners.
3. **Empty states com ilustração simples e CTA** — "Nenhuma compra ainda. Cadastre a primeira →".
4. **Inline validation** em forms (mensagem em vermelho abaixo do campo).
5. **Atalhos de teclado** no formulário de compra/venda: `Tab` navega, `Enter` adiciona linha, `Ctrl+S` salva.
6. **Filtros sempre persistem** na URL (querystring) para o usuário poder compartilhar/salvar.

---

## 5. Como usar este briefing

Para cada tela em `§3`, peça ao Stitch:

> *"Gere um mockup web em dark mode da tela `[nome da tela]` do TradeFlow conforme o briefing. Use a paleta `[§1]` e o shell padrão `[§2]`. Foco: `[descrição da seção]`."*

Comece pelas telas críticas (3.2 Dashboard, 3.4 Compra-form, 3.6 Venda-form, 3.14 Fluxo de Caixa) — elas concentram 80% da complexidade visual. As demais herdam padrões.
