<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $table = 'customer_address';

    public function Orders()
    {
    	return $this->hasMany('App\Order');
    }

    public function Customer()
    {
		  return $this->belongsTo('App\Customer');
    }

    public function addressType()
    {
		  return $this->belongsTo('App\addressType');
    }
    
    public function Location()
    {
		  return $this->belongsTo('App\Location');
    }
}
