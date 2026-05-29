<?php

namespace App\Models\Purchases;

use App\Models\Business\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompraItem extends Model
{
    /** @use HasFactory<\Database\Factories\Purchases\CompraItemFactory> */
    use HasFactory;

    public $table = 'compra_itens';

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

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
