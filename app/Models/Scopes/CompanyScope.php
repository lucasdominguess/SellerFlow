<?php

namespace App\Models\Scopes;

use App\Classes\AuthContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

// Isola as leituras por empresa do usuário autenticado (barra IDOR entre tenants).
// Aplica-se a index e ao route-model binding (show/update/delete viram 404 para outra empresa).
class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Sem token (commands, seeders, jobs) não há escopo. Em HTTP o JwtMiddleware garante o token.
        if (! AuthContext::check()) {
            return;
        }

        $builder->whereIn(
            $model->getTable() . '.company_id',
            AuthContext::companyIds()->all()
        );
    }
}
