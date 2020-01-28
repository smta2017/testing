<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function OrderProducts()
    {
    	return $this->hasMany('App\OrderProduct');
    }

    public function ProductPrices()
    {
    	return $this->hasMany('App\ProductPrice');
    }

    public function CustomerPreferencies()
    {
    	return $this->hasMany('App\CustomerPreference');
    }

    public function OrderFastPreferencies()
    {
    	return $this->hasMany('App\OrderFastPreference');
    }

    
}