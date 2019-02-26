<?php

namespace Banjir;

use Illuminate\Database\Eloquent\Model;

class ParameterModel extends Model
{
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
