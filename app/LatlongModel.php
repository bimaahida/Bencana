<?php

namespace Banjir;

use Illuminate\Database\Eloquent\Model;

class LatlongModel extends Model
{
    protected $table = 'latlong';
    protected $primaryKey= 'id';
    protected $fillable = [
        'latitude',
        'longitude',
        'area_id',
    ];

    public function area(){
        return $this->hasOne('Banjir\AreaModel');
    } 
}
