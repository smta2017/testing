<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderFastPreference extends Model
{
    protected $table = 'order_fast_preference';
    
    public function Order()
    {
		  return $this->belongsTo('App\Order');
    }

    public function OrderProduct()
    {
		  return $this->belongsTo('App\OrderProduct');
    }

}
