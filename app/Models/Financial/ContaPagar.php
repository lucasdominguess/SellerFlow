<?php

namespace App\Models\Financial;

use App\Models\Accout\User;
use App\Models\Business\Fornecedor;
use App\Models\ListSuspended\CategoriaFinanceira;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\FormaPagamento;
use App\Models\Purchases\Compra;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaPagar extends Model
{
    /** @use HasFactory<\Database\Factories\Financial\ContaPagarFactory> */
    use HasFactory;

    public $table = 'contas_pagar';

    protected $fillable = [
        'company_id',
        'user_id',
        'categoria_financeira_id',
        'forma_pagamento_id',
        'fornecedor_id',
        'compra_id',
        'descricao',
        'valor',
        'vencimento',
        'status',
        'data_pagamento',
        'observacao',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'vencimento'       => 'date',
        'data_pagamento'   => 'date',
        'valor'            => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoriaFinanceira()
    {
        return $this->belongsTo(CategoriaFinanceira::class, 'categoria_financeira_id');
    }

    public function formaPagamento()
    {
        return $this->belongsTo(FormaPagamento::class, 'forma_pagamento_id');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }
}
