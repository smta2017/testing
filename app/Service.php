<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function OrderProducts()
    {
    	return $this->hasMany('App\OrderProduct');
    }
}
