<?php

namespace App\Models\Financial;

use App\Models\Accout\User;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use App\Models\Sales\Venda;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaReceber extends Model
{
    /** @use HasFactory<\Database\Factories\Financial\ContaReceberFactory> */
    use HasFactory;

    public $table = 'contas_receber';

    protected $fillable = [
        'company_id',
        'user_id',
        'market_place_id',
        'venda_id',
        'descricao',
        'valor_bruto',
        'taxa_marketplace',
        'valor_frete',
        'valor_liquido',
        'previsao_recebimento',
        'data_recebimento_real',
        'status',
        'observacao',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'previsao_recebimento'   => 'date',
        'data_recebimento_real'  => 'date',
        'valor_bruto'            => 'decimal:2',
        'taxa_marketplace'       => 'decimal:2',
        'valor_frete'            => 'decimal:2',
        'valor_liquido'          => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(MarketPlace::class, 'market_place_id');
    }

    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }
}
