<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Order as Order;

class CustomerOrder extends JsonResource
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
            "status" => 1,
            "message" => "Successfully retrieved orders against customer.",
            "orders" =>Order::collection($this->Orders),
            "cancellation_fee" => $this->cancellation_fee,
            "cancellation_buffer" => $this->cancellation_buffer,
            "pickupRescStartBuffer" => $this->pickupRescStartBuffer,
            "deliveryRescEndBuffer" => $this->deliveryRescEndBuffer,
            "rescheduling_fee" => $this->rescheduling_fee
        ];
    }
}
