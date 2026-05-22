<?php

namespace App\Models\Business;

use App\Models\ListSuspended\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    /** @use HasFactory<\Database\Factories\\Business\FornecedorFactory> */
    use HasFactory;

    public $table = 'fornecedores';
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
