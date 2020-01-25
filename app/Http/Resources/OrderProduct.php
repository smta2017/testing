<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderProduct extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return[
            "product_name"=> $this->product_name,
            "product_count"=> $this->product_count,
            "product_price"=> $this->product_price,
            "product_total"=>($this->product_count*$this->product_price),
        ];
    }
}
