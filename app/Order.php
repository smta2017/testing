<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function OrderProducts()
    {
    	return $this->hasMany('App\OrderProduct');
    }

    public function OrderFastPreferencies()
    {
    	return $this->hasMany('App\OrderFastPreference');
    }
    
    // -----------
    public function Customer()
    {
		  return $this->belongsTo('App\Customer');
    }

    public function OrderStatus()
    {
		  return $this->belongsTo('App\OrderStatus');
    }
    
    public function CustomerAddress()
    {
		  return $this->belongsTo('App\CustomerAddress','address_id');
    }   
}