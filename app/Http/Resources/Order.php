<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            "id"=>$this->id,
            "total_price"=>$this->total_price,
            "pickup_date"=>$this->pickup_date,
            "pickup_start"=>$this->pickup_start,
            "delivery_date"=>$this->delivery_date,
            "delivery_end"=>$this->delivery_end,
            "created_at"=>$this->created_at,
            "status"=>$this->status,
            "product_count"=>$this->product_count,
            "is_rated"=>$this->is_rated,
        ];
    }
}
