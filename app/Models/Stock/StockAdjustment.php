<?php

namespace App\Models\Stock;

use App\Models\Accout\User;
use App\Models\Business\Product;
use App\Models\ListSuspended\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    /** @use HasFactory<\Database\Factories\Stock\AjusteEstoqueFactory> */
    use HasFactory;

    public $table = 'stock_adjustments';

    protected $fillable = [
        'company_id',
        'product_id',
        'user_id',
        'quantidade',
        'motivo',
        'observacao',
    ];

    protected $hidden = ['created_at', 'updated_at'];

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

    public function movimentacao()
    {
        return $this->hasOne(Stock::class, 'origem_id')
            ->where('origem_tipo', 'ajuste_manual');
    }
}
