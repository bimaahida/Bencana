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
}
