<?php

namespace App\Models\ListSuspended;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaPagamento extends Model
{
    /** @use HasFactory<\Database\Factories\\ListSuspended\FormaPagamentoFactory> */
    use HasFactory;

    public $table = 'forma_pagamentos';
    public $timestamps = false;

    public $fillable = ['name','description'];
}
