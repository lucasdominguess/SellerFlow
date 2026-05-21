<?php

namespace App\Models\ListSuspended;

use App\Models\Accout\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\\ListSuspended\CompanyFactory> */
    use HasFactory;

    public $table = 'companies';
    public$timestamps = false;

      public $fillable = ['name','cnpj','description','status_id'];

      public function stores()
      {
          return $this->hasMany(Store::class, 'company_id');
      }
      public function status()
        {
            return $this->belongsTo(Status::class, 'status_id');
        }

}
