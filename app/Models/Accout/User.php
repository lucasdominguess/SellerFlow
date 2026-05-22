<?php

namespace App\Models\Accout;

use App\Models\ListSuspended\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    /** @use HasFactory<\Database\Factories\Accout\UserFactory> */
    use HasFactory;

    public $table ='users';
    public $fillable = ['name','email','password','status_id'];
    public $hidden = ['created_at', 'updated_at'];

    public $casts = [
        'password'=> 'hashed',
    ];

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
