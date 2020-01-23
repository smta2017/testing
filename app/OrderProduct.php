<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    public function Order()
    {
		  return $this->belongsTo('App\Order');
    }

    public function Product()
    {
		  return $this->belongsTo('App\Product');
    }
}