<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $table = 'product_price';

    public function Product()
    {
		  return $this->belongsTo('App\Product');
    }

    public function Service()
    {
		  return $this->belongsTo('App\Service');
    }
}
