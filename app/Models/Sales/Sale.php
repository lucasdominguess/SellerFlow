<?php

namespace App\Models\Sales;

use App\Classes\AuthContext;
use App\Enums\TransactionStatus;
use App\Models\Concerns\BelongsToCompany;
use App\Models\Accout\Store;
use App\Models\Accout\User;
use App\Models\Finance\AccountReceivable;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use App\Models\Stock\Stock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    /** @use HasFactory<\Database\Factories\Sales\SaleFactory> */
    use HasFactory;
    use BelongsToCompany;

    public $table = 'sales';

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

    // FK explícita: a coluna continua 'venda_id' (não renomeada)
    public function itens()
    {
        return $this->hasMany(SaleItem::class, 'venda_id');
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
