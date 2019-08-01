<?php

namespace Banjir;

use Illuminate\Database\Eloquent\Model;

class ModelModel extends Model
{
    public $timestamps = false;
    
    protected $table = 'model';
    
    protected $primaryKey= 'id';
    
    protected $fillable = [
        'name',
        'min_id',
        'max_id',
        'date',
        'praman',
        'prrawan',
        'hrr',
        'hra',
        'hsr',
        'hsa',
        'htr',
        'hta',
        'klr',
        'kla',
        'ksr',
        'ksa',
        'kcr',
        'kca',
        'snr',
        'sna',
        'srr',
        'sra',
    ];
}
