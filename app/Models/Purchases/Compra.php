<?php

namespace App\Models\Purchases;

use App\Models\Accout\Store;
use App\Models\Accout\User;
use App\Models\Business\Fornecedor;
use App\Models\Financial\ContaPagar;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\FormaPagamento;
use App\Models\ListSuspended\Status;
use App\Models\Stock\MovimentacaoEstoque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    /** @use HasFactory<\Database\Factories\Purchases\CompraFactory> */
    use HasFactory;

    public $table = 'compras';

    protected $fillable = [
        'company_id',
        'store_id',
        'fornecedor_id',
        'user_id',
        'forma_pagamento_id',
        'status_id',
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
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formaPagamento()
    {
        return $this->belongsTo(FormaPagamento::class, 'forma_pagamento_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function itens()
    {
        return $this->hasMany(CompraItem::class);
    }

    public function contasPagar()
    {
        return $this->hasMany(ContaPagar::class, 'compra_id');
    }

    public function movimentacoes()
    {
        return $this->hasMany(MovimentacaoEstoque::class, 'origem_id')
            ->where('origem_tipo', 'compra');
    }
}
