<?php

namespace App\Models\Accout;

use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use App\Models\ListSuspended\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    /** @use HasFactory<\Database\Factories\\Accout\StoreFactory> */
    use HasFactory;

    public $table = 'stores';
    public $timestamps = false;

    public $fillable =['name','email','description','status_id','marketplace_id','company_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_stores', 'store_id', 'user_id')
                    ->withPivot('role_id', 'status_id');
    }
    public function userStores()
    {
        return $this->hasMany(UserStore::class, 'store_id');
    }
    public function marketplace()
    {
        return $this->belongsTo(MarketPlace::class, 'marketplace_id');
    }
        public function company()
        {
            return $this->belongsTo(Company::class, 'company_id');
        }
        public function status()
        {
            return $this->belongsTo(Status::class, 'status_id');
        }

}
