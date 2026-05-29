<?php

namespace App\Models\Accout;

use App\Models\Accout\CompanyUser;
use App\Models\ListSuspended\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\Accout\UserFactory> */
    use HasFactory;

    public $table    = 'users';
    public $fillable = ['name', 'email', 'password', 'status_id'];
    public $hidden   = ['password', 'created_at', 'updated_at'];

    public $casts = [
        'password' => 'hashed',
    ];

    // --- JWTSubject ---

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        $companyUser = $this->companyUsers()->with('company', 'role')->get()->map(fn ($companyUser) => [
            'company_id'   => $companyUser->company_id,
            'company_name' => $companyUser->company?->name,
            'status_id'   => $companyUser->status_id,
            'role_id'      => $companyUser->role_id,
            'role_name'    => $companyUser->role?->name,
        ]);

        $stores = $this->stores()->get()->map(fn ($store) => [
            'store_id'   => $store->id,
            'store_name' => $store->name,
            'role_id'    => $store->pivot->role_id,
            'status_id'  => $store->pivot->status_id,
        ]);

        return [
            'user' => [
                'id'    => $this->id,
                'name'  => $this->name,
                'email' => $this->email,
                'status_id' => $this->status_id,
            ],
            'company' => $companyUser,
            'stores' => $stores,
        ];
    }

    // --- Relações ---

    public function companyUsers()
    {
        return $this->hasMany(CompanyUser::class, 'user_id');
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'user_stores', 'user_id', 'store_id')
                    ->withPivot('role_id', 'status_id');
    }

    public function userStores()
    {
        return $this->hasMany(UserStore::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
