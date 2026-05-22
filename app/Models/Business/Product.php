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
        'path_image'
    ];
    public $hidden = [
        'created_at',
        'updated_at'
    ];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

}
