<?php

namespace App\Models\Stock;

use App\Models\Accout\User;
use App\Models\Business\Product;
use App\Models\ListSuspended\Company;
use Illuminate\Database\Eloquent\Model;

// Saldo materializado por produto/empresa. Mantido pelo StockObserver a cada
// movimentação e reconstruível via `php artisan stock:rebuild-balances`.
class StockBalance extends Model
{
    public $table = 'stock_balances';

    protected $fillable = [
        'company_id',
        'product_id',
        'total_entradas',
        'total_saidas',
        'total_ajustes_positivos',
        'total_ajustes_negativos',
        'saldo_atual',
        'last_adjustment_user_id',
    ];

    protected $casts = [
        'total_entradas' => 'integer',
        'total_saidas' => 'integer',
        'total_ajustes_positivos' => 'integer',
        'total_ajustes_negativos' => 'integer',
        'saldo_atual' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function lastAdjustmentUser()
    {
        return $this->belongsTo(User::class, 'last_adjustment_user_id');
    }
}
