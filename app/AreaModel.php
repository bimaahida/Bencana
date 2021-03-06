<?php

namespace Banjir;

use Illuminate\Database\Eloquent\Model;

class AreaModel extends Model
{
    public $timestamps = false;
    
    protected $table = 'area';
    
    protected $primaryKey= 'id';
    
    protected $fillable = [
        'name',
    ];

    public function location(){
        return $this->hasMany('Banjir\LocationModel','area_id',$this->primaryKey);
    }
}
