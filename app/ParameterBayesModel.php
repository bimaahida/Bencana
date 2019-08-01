<?php

namespace Banjir;

use Illuminate\Database\Eloquent\Model;

class ParameterBayesModel extends Model
{
    public $timestamps = false;
    
    protected $table = 'parameter_bayes';
    
    protected $primaryKey= 'id';
    
    protected $fillable = [
        'rainfall',
        'soil',
        'slope',
        'date',
        'status',
        'location_id',
    ];
}
