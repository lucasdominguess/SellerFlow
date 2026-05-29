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
        $companyUser = $this->companyUsers()->with('company', 'role')->first();

        return [
            'company_id'   => $companyUser?->company_id,
            'company_name' => $companyUser?->company?->name,
            'role_id'      => $companyUser?->role_id,
            'role_name'    => $companyUser?->role?->name,
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
