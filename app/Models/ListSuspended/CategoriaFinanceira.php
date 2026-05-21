<?php

namespace App\Models\ListSuspended;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaFinanceira extends Model
{
    /** @use HasFactory<\Database\Factories\\ListSuspended\CategoriaFinanceiraFactory> */
    use HasFactory;

        public $table = 'categoria_financeiras';
    public $timestamps = false;

    public $fillable = ['name','description'];
}
