<?php

namespace Banjir;

use Illuminate\Database\Eloquent\Model;

class ParameterBayesModel extends Model
{
    public $timestamps = false;
    
    protected $table = 'parameter_Bayes';
    
    protected $primaryKey= 'id';
    
    protected $fillable = [
        'rainfall',
        'soil',
        'slope',
        'date',
        'status',
        'area_id',
    ];
}
