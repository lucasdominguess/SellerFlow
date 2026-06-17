<?php

namespace App\Models\Finance;

use App\Enums\TransactionStatus;
use App\Models\ListSuspended\FinancialCategory;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\PaymentMethod;
use App\Models\Purchases\Purchase;
use App\Models\Sales\Sale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPayable extends Model
{
    /** @use HasFactory<\Database\Factories\Finance\AccountPayableFactory> */
    use HasFactory;

    public $table = 'account_payables';

    protected $fillable = [
        'valor',
        'vencimento',
        'pago_em',
        'status',
        'categoria_financeira_id',
        'forma_pagamento_id',
        'origem_tipo',
        'origem_id',
        'company_id',
        'observacao',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'vencimento' => 'date',
        'pago_em'    => 'date',
        'valor'      => 'decimal:2',
        'status'     => TransactionStatus::class,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function categoriaFinanceira()
    {
        return $this->belongsTo(FinancialCategory::class);
    }

    public function formaPagamento()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function compra()
    {
        return $this->belongsTo(Purchase::class, 'origem_id');
    }

    public function venda()
    {
        return $this->belongsTo(Sale::class, 'origem_id');
    }
}
