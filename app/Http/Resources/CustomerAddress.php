<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddress extends JsonResource
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
            "address_id"=> $this->id,
            "latitude"=> $this->latitude,
            "location_id"=> $this->location_id,
            "longitude"=> $this->longitude,
            "building_no"=> $this->building_no,
            "street_address"=> $this->street_address,
            "floor_no"=> $this->floor_no,
            "apartment_no"=> $this->apartment_no,
            "address_type"=> "home",
            "additional_directions"=> $this->additional_directions,
            "is_default"=> $this->is_default
        ];
    }
}
