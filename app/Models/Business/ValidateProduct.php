<?php

namespace App\Models\Business;

use App\Models\Accout\User;
use App\Models\Business\Fornecedor;
use App\Models\ListSuspended\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidateProduct extends Model
{
    use HasFactory;

    /**
     * Apenas inputs editáveis pelo usuário.
     * Identidade (company_id, user_id) e valores calculados
     * (profit_*, breakeven_roas, fee_*) são definidos pelo Service,
     * nunca por mass assignment.
     */
    protected $fillable = [
        'name',
        'brand',
        'description',
        'catalog_link',
        'fornecedor_id',
        'price_sale',
        'price_buy',
        'cust_additional',

        'company_id',
        'user_id',
        'profit_amount',
        'profit_margin',
        'breakeven_roas',

        'fee_percent',
        'fee_fixed',
    ];

    protected $casts = [
        'price_sale'      => 'decimal:2',
        'price_buy'       => 'decimal:2',
        'cust_additional' => 'decimal:2',
        'fee_percent'     => 'decimal:2',
        'fee_fixed'       => 'decimal:2',
        'profit_amount'   => 'decimal:2',
        'profit_margin'   => 'decimal:2',
        'breakeven_roas'  => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }
}
