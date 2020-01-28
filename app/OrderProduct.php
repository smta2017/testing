<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    public function OrderFastPreferencies()
    {
    	return $this->hasMany('App\OrderFastPreference');
    }
    
    public function Order()
    {
		  return $this->belongsTo('App\Order');
    }
    
    public function ProductPrice()
    {
		  return $this->belongsTo('App\ProductPrice');
    }

    // public function Product()
    // {
	// 	  return $this->belongsTo('App\Product');
    // }

    // public function Service()
    // {
	// 	  return $this->belongsTo('App\Service');
    // }

    

}