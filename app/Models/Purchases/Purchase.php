<?php

namespace App\Models\Purchases;

use App\Models\Accout\Store;
use App\Models\Accout\User;
use App\Enums\TransactionStatus;
use App\Models\Business\Supplier;
use App\Models\Concerns\BelongsToCompany;
use App\Models\Finance\AccountPayable;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\PaymentMethod;
use App\Models\Stock\Stock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    /** @use HasFactory<\Database\Factories\Purchases\PurchaseFactory> */
    use HasFactory;
    use BelongsToCompany;

    public $table = 'purchases';

    protected $fillable = [
        'company_id',
        'store_id',
        'fornecedor_id',
        'user_id',
        'forma_pagamento_id',
        'status',
        'numero_nota',
        'data_compra',
        'valor_total',
        'numero_parcelas',
        'observacao',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'data_compra'  => 'date',
        'valor_total'  => 'decimal:2',
        'status'       => TransactionStatus::class,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    // método 'fornecedor' mantido: a FK continua 'fornecedor_id'
    public function fornecedor()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formaPagamento()
    {
        return $this->belongsTo(PaymentMethod::class, 'forma_pagamento_id');
    }

    // FK explícita: a coluna continua 'compra_id' (não renomeada)
    public function itens()
    {
        return $this->hasMany(PurchaseItem::class, 'compra_id');
    }

    public function contasPagar()
    {
        return $this->hasMany(AccountPayable::class, 'origem_id')
            ->where('origem_tipo', 'compra');
    }

    public function movimentacoes()
    {
        return $this->hasMany(Stock::class, 'origem_id')
            ->where('origem_tipo', 'compra');
    }
}
