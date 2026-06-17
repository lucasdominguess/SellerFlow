<?php

namespace App\Models\Sales;

use App\Models\Business\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    /** @use HasFactory<\Database\Factories\Sales\SaleItemFactory> */
    use HasFactory;

    public $table = 'sale_items';

    protected $fillable = [
        'venda_id',
        'product_id',
        'quantidade',
        'valor_unitario',
        'valor_total',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'valor_unitario' => 'decimal:2',
        'valor_total'    => 'decimal:2',
    ];

    // método 'venda' mantido: a FK continua 'venda_id'
    public function venda()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
