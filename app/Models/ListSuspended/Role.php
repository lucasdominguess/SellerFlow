<?php

namespace App\Models\ListSuspended;

use App\Models\Accout\UserStore;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public $table = 'roles';
    public $timestamps = false;

    public $fillable = ['name'];

    public function userStores()
    {
        return $this->hasMany(UserStore::class, 'id_role');
    }

}
