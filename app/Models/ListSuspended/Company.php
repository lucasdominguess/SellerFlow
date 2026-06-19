<?php

namespace App\Models\ListSuspended;

use App\Enums\Status as StatusEnum;
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

      // Default no create (a coluna não tem default efetivo na migration): nova empresa
      // nasce pendente. Não afeta updates — $attributes só semeia novas instâncias.
      protected $attributes = [
          'status_id' => StatusEnum::PENDING->value,
      ];

      public function stores()
      {
          return $this->hasMany(Store::class, 'company_id');
      }
      public function status()
        {
            return $this->belongsTo(Status::class, 'status_id');
        }

}
