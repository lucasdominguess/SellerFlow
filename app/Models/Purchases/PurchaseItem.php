<?php

namespace App\Models\Purchases;

use App\Models\Business\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    /** @use HasFactory<\Database\Factories\Purchases\PurchaseItemFactory> */
    use HasFactory;

    public $table = 'purchase_items';

    protected $fillable = [
        'compra_id',
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

    // método 'compra' mantido: a FK continua 'compra_id'
    public function compra()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
