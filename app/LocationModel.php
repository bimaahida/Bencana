<?php

namespace Banjir;

use Illuminate\Database\Eloquent\Model;

class LocationModel extends Model
{
    public $timestamps = false;

    protected $table = 'location';
    
    protected $primaryKey= 'id';
    
    protected $fillable = [
        'slope',
        'soil',
        'latitude',
        'longitude',
        'colum',
        'row',
        'area_id',
    ];

    public function area(){
        return $this->belongsTo('Banjir\AreaModel','area_id',$this->primaryKey);
    }
    public function params(){
        return $this->hasMany('Banjir\ParameterModel','location_id',$this->primaryKey);
    } 
    public function paramsNaiveBayes(){
        return $this->hasMany('Banjir\ParameterBayesModel','location_id',$this->primaryKey);
    } 
}
