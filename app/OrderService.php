<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    public function Order()
    {
		  return $this->belongsTo('App\Order');
    }

    public function Service()
    {
		  return $this->belongsTo('App\Service');
    }
}
