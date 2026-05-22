<?php

namespace App\Models\Accout;

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
        return [
            
        ];
    }

    // --- Relações ---

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
