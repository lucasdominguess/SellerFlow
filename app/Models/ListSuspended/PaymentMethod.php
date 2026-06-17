<?php

namespace App\Models\ListSuspended;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    /** @use HasFactory<\Database\Factories\ListSuspended\PaymentMethodFactory> */
    use HasFactory;

    public $table = 'payment_methods';
    public $timestamps = false;

    public $fillable = ['name', 'description'];
}
