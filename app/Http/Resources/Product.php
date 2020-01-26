<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Product extends JsonResource
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
            "id" => $this->id,
            "name" => $this->Product->name,
            "price" => $this->price,
            "image" => asset('images/product-images/'. $this->Product->image),
            "preferences" => $this->Product->CustomerPreference
        ];
    }
}
