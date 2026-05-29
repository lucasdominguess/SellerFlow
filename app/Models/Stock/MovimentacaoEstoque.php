<?php

namespace App\Models\Stock;

use App\Models\Accout\User;
use App\Models\Business\Product;
use App\Models\ListSuspended\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimentacaoEstoque extends Model
{
    /** @use HasFactory<\Database\Factories\Stock\MovimentacaoEstoqueFactory> */
    use HasFactory;

    public $table = 'movimentacoes_estoque';

    protected $fillable = [
        'company_id',
        'product_id',
        'user_id',
        'tipo',
        'quantidade',
        'origem_tipo',
        'origem_id',
        'observacao',
    ];

    protected $hidden = ['updated_at'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
