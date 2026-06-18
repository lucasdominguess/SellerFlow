<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    public $table = 'product_images';

    protected $fillable = [
        'product_id',
        'path',
        'position',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
