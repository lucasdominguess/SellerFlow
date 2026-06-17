<?php

namespace App\Models\ListSuspended;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ListSuspended\FinancialCategoryFactory> */
    use HasFactory;

    public $table = 'financial_categories';
    public $timestamps = false;

    public $fillable = ['name', 'description'];
}
