<?php

namespace Banjir;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    public $timestamps = false;
    
    protected $table = 'user';
    
    protected $primaryKey= 'id';
    
    protected $fillable = [
        'username',
        'password',
        'name',
    ];
}
