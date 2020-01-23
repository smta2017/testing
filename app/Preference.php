<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    protected $table = 'preference';
    
    public function Product()
    {
		return $this->belongsTo('App\Product');
    }
}