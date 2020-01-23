<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function OrderProducts()
    {
    	return $this->hasMany('App\OrderProduct');
    }
}