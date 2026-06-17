<?php

namespace App\Models\Business;

use App\Models\ListSuspended\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\Business\SupplierFactory> */
    use HasFactory;

    public $table = 'suppliers';
    protected $fillable = [
        'name',
        'responsavel',
        'cnpj',
        'email',
        'phone',
        'address',
        'link_catalog',
        'description',
        'status_id'
    ];
    public $hidden = [
        'created_at',
        'updated_at'
    ];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
