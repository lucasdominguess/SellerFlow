<?php

use App\Models\Accout\CompanyUser;
use App\Models\Accout\User;
use App\Models\ListSuspended\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

// Autentica via JWT real: gera o token e injeta no header Authorization das próximas
// requisições, para passar pelo JwtMiddleware (que faz parseToken do header).
function actingAsJwt(?User $user = null): User
{
    $user ??= User::factory()->create();

    $token = auth('api')->login($user);
    test()->withHeader('Authorization', "Bearer {$token}");

    return $user;
}

// Autentica via JWT um usuário VINCULADO a uma empresa (CompanyUser), necessário para os
// módulos com tenant scope (vendas, compras, estoque, finanças): sem o vínculo, a claim
// 'company' do token fica vazia e o CompanyScope filtra tudo. Retorna ['user' => , 'company' => ].
function actingAsCompanyJwt(?Company $company = null): array
{
    // FKs de lookup exigidas por User/Company/CompanyUser
    DB::table('status')->insertOrIgnore([
        ['id' => 1, 'name' => 'Ativo'],
        ['id' => 2, 'name' => 'Inativo'],
    ]);
    DB::table('roles')->insertOrIgnore([['id' => 1, 'name' => 'admin']]);

    $company ??= Company::factory()->create();
    $user = User::factory()->create();

    CompanyUser::factory()->create([
        'company_id' => $company->id,
        'user_id'    => $user->id,
        'role_id'    => 1,
        'status_id'  => 1,
    ]);

    $token = auth('api')->login($user);
    test()->withHeader('Authorization', "Bearer {$token}");

    return ['user' => $user, 'company' => $company];
}
