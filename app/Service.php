<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function OrderProducts()
    {
    	return $this->hasMany('App\OrderProduct');
    }
    
     public function OrderServices()
    {
    	return $this->hasMany('App\OrderService');
    }

    public function ProductPrices()
    {
    	return $this->hasMany('App\ProductPrice');
    }
}
