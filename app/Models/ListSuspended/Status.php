<?php

namespace App\Models\ListSuspended;

use App\Models\Accout\Store;
use App\Models\Accout\UserStore;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    public $table ='status';
    public $timestamps = false;
     public $fillable = ['name'];

     public function companies()
     {
         return $this->hasMany(Company::class, 'status_id');
     }
     public function marketplaces()
     {
         return $this->hasMany(MarketPlace::class, 'status_id');
     }
        public function stores()
        {
            return $this->hasMany(Store::class, 'status_id');
        }
        public function userStores()
        {
            return $this->hasMany(UserStore::class, 'id_status');
        }
}
