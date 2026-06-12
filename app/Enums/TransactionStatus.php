<?php

namespace App\Enums;

// Status unificado do ciclo de vida de compras, vendas e contas (pagar/receber).
// COMPLETED representa "concluído" na compra/venda, "pago" na conta a pagar e
// "recebido" na conta a receber. OVERDUE é exclusivo do financeiro (calculado
// por vencimento) — nunca é definido a partir de uma compra/venda.
enum TransactionStatus: string
{
    case PENDING   = 'pendente';
    case COMPLETED = 'concluido';
    case OVERDUE   = 'atrasado';
    case CANCELED  = 'cancelado';
}
