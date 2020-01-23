<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CustomerAddress as CustomerAddressResource;
use App\Http\Resources\CustomerPreference as CustomerPreferenceResource;

class CustomerProfile extends JsonResource
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
        // dd($this->attributes);
        return [
            'status'=>1,
            'message'=>'Successfully retrieved customer profile.',
            'customer'=>[
                "id"=> $this->id,
                "email"=> $this->email,
                "phone"=> $this->phone,
                "country_code"=> $this->country_code,
                "first_name"=> $this->first_name,
                "last_name"=> $this->last_name,
                "jwt_token"=> $this->jwt_token,
                "is_signedOut"=> $this->is_signedOut,

                "number_of_orders"=>$this->Orders->count(),
                "total_items_sent"=>0,
                "total_amount_spent"=>$this->Orders->sum('total_price'),
                "version_no"=> $this->version_no,
            ],
            'address'=>CustomerAddressResource::collection($this->CustomerAddresses),
            "preference"=>CustomerPreferenceResource::collection($this->CustomerPreferencies)

        ];

    }
}
