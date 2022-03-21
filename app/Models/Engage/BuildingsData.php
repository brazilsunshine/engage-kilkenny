<?php

namespace App\Models\Engage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingsData extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function building ()
    {
        return $this->hasOne(Building::class, 'osm_id', 'osm_id');
    }
}
