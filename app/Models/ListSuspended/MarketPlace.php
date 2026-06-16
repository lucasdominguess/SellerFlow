<?php

namespace App\Models\ListSuspended;

use App\Models\Accout\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketPlace extends Model
{
    /** @use HasFactory<\Database\Factories\\ListSuspended\MarketPlaceFactory> */
    use HasFactory;

    public $table = 'market_places';
    public $timestamps = false;

    public $fillable = ['name', 'description', 'taxa_percentual', 'taxa_fixa', 'status_id'];

    public function stores()
    {
        return $this->hasMany(Store::class, 'marketplace_id');
    }
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

}
