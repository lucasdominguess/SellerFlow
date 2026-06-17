<?php

namespace App\Models\Finance;

use App\Enums\TransactionStatus;
use App\Models\Accout\Store;
use App\Models\ListSuspended\Company;
use App\Models\Purchases\Purchase;
use App\Models\Sales\Sale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountReceivable extends Model
{
    /** @use HasFactory<\Database\Factories\Finance\AccountReceivableFactory> */
    use HasFactory;

    public $table = 'account_receivables';

    protected $fillable = [
        'valor',
        'previsao_recebimento',
        'recebido_em',
        'status',
        'origem_tipo',
        'origem_id',
        'company_id',
        'store_id',
        'observacao',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'previsao_recebimento' => 'date',
        'recebido_em'          => 'date',
        'valor'                => 'decimal:2',
        'status'               => TransactionStatus::class,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function compra()
    {
        return $this->belongsTo(Purchase::class, 'origem_id');
    }

    public function venda()
    {
        return $this->belongsTo(Sale::class, 'origem_id');
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
