<?php

namespace Banjir;

use Illuminate\Database\Eloquent\Model;

class Bencana extends Model
{
    protected $fillable = [
        'river',
        'watershed',
        'raifall',
        'slope',
        'soil',
      ];
}
