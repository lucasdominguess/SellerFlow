<?php

namespace App\Models\Accout;

use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\Role;
use App\Models\ListSuspended\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    /** @use HasFactory<\Database\Factories\Accout\CompanyUserFactory> */
    use HasFactory;

    public $table    = 'company_users';
    public $fillable = ['company_id', 'user_id', 'role_id', 'status_id'];
    public $hidden   = ['created_at', 'updated_at'];

    public $timestamps = false;

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
