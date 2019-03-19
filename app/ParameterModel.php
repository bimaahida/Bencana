<?php

namespace Banjir;

use Illuminate\Database\Eloquent\Model;

class ParameterModel extends Model
{
    public $timestamps = false;
    
    protected $table = 'parameter';
    
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
