<?php

namespace App\Models\Accout;

use App\Models\ListSuspended\Role;
use App\Models\ListSuspended\Status;
use Illuminate\Database\Eloquent\Model;

class UserStore extends Model
{
    public $table = 'user_stores';

    public $timestamps = false;

    public $fillable = ['user_id','store_id','role_id','status_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
