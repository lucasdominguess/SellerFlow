<?php

namespace App\Models\Business;

use App\Models\ListSuspended\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\\Business\ProductFactory> */
    use HasFactory;

    public $table = 'products';
    protected $fillable = [
        'sku',
        'name',
        'marca',
        'description',
        'price_unit',
        'price_box',
        'status_id',
        'fornecedor_id',
    ];
    public $hidden = [
        'created_at',
        'updated_at'
    ];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'fornecedor_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('position');
    }

}
