<?php

namespace Banjir;

use Illuminate\Database\Eloquent\Model;

class LatlongModel extends Model
{
    public $timestamps = false;

    protected $table = 'latlong';
    
    protected $primaryKey= 'id';
    
    protected $fillable = [
        'latitude',
        'longitude',
        'area_id',
    ];

    public function area(){
        return $this->belongsTo('Banjir\AreaModel','area_id',$this->primaryKey);
    } 
}
