<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    //
    protected $table = 'hotels';

    public function iterinary()
    {
    	return $this->belongsTo('App\Iterinary');
    }
}
