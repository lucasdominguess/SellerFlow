<?php

namespace App\Models\Sales;

use App\Classes\AuthContext;
use App\Enums\TransactionStatus;
use App\Models\Accout\Store;
use App\Models\Accout\User;
use App\Models\Finance\AccountReceivable;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use App\Models\Stock\Stock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    /** @use HasFactory<\Database\Factories\Sales\VendaFactory> */
    use HasFactory;

    public $table = 'vendas';

    protected $fillable = [
        'company_id',
        'store_id',
        'market_place_id',
        'user_id',
        'numero_pedido',
        'data_venda',
        'valor_bruto',
        'taxa_marketplace',
        'valor_frete',
        'valor_liquido',
        'data_previsao_repasse',
        'status',
        'observacao',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'data_venda'             => 'date',
        'data_previsao_repasse'  => 'date',
        'valor_bruto'            => 'decimal:2',
        'taxa_marketplace'       => 'decimal:2',
        'valor_frete'            => 'decimal:2',
        'valor_liquido'          => 'decimal:2',
        'status'                 => TransactionStatus::class,
    ];

    // tenant scoping no route model binding: só resolve vendas da empresa do
    // usuário logado. Se o id pertencer a outra empresa, devolve 404 (fail-closed).
    // public function resolveRouteBinding($value, $field = null)
    // {
    //     return $this->where($field ?? $this->getRouteKeyName(), $value)
    //         ->where('company_id', AuthContext::companyId())
    //         ->firstOrFail();
    // }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(MarketPlace::class, 'market_place_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itens()
    {
        return $this->hasMany(VendaItem::class);
    }

    public function contaReceber()
    {
        return $this->hasOne(AccountReceivable::class, 'origem_id')
            ->where('origem_tipo', 'venda');
    }

    public function movimentacoes()
    {
        return $this->hasMany(Stock::class, 'origem_id')
            ->where('origem_tipo', 'venda');
    }
}
