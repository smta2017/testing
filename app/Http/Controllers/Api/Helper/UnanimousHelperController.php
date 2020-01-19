<?php

namespace App\Http\Controllers\Api\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\GlobalSetting;
use App\Customer;
use App\CustomerAddress;
use App\Location;
use App\TimeSlot;
use JWTAuth;
use DB;

class UnanimousHelperController extends Controller
{
   
    public function updateSignedOut(Request $request){
        // $this->authenticate($request);
        if ($request->getMethod()=="POST") { 
             JWTAuth::setToken($request->header('Authorization'));
            $claim = JWTAuth::getPayload();
            $customer = Customer::find($claim['sub']);
            $customer->is_signedOut = 1;
            if($customer->update())
            {
                $success_arr=array(
                    "status" => 1,
                    "message" => "Signed Out Updated Successfully."
                );
            }else{
                $success_arr=array(
                    "status" => 0,
                    "message" => "Signed Out Updation Failed."
                );
            }
            return json_encode($success_arr,JSON_NUMERIC_CHECK);   
        }else{
            $success_arr=array(
                "status" => 0,
                "message" => "Signed Out Updated Failed."
            );
            return json_encode($success_arr,JSON_NUMERIC_CHECK);   
        }
    }

    public function getProfile($id = null,Request $request){
        // $this->authenticate($request);
   
        $success_arr= array(
            'status'=>1,
            'message'=>'Successfully retrieved customer profile.',
            'customer'=>[
                    "id" =>4904,
                    "email" =>"smta0@yahoo.com",
                    "phone" =>"01274200778",
                    "country_code" =>"2",
                    "first_name" =>"sameh",
                    "last_name" =>"taha",
                    "jwt_token" =>"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBpLm1ha3dhYXBwLmNvbVwvYXBpXC9yZWdpc3RlciIsImlhdCI6MTU3OTExMjUxMiwiZXhwIjoxNTc5MTE2MTEyLCJuYmYiOjE1NzkxMTI1MTIsImp0aSI6IlhTVkV1dGJ5TjZHMDN5WXMiLCJzdWIiOm51bGwsInBydiI6IjhiNDIyZTZmNjU3OTMyYjhhZWJjYjFiZjFlMzU2ZGQ3NmEzNjViZjIifQ.PsnYb61hmLFlzlOT1elC8aMfkzA3EKHcHrQcP1c0sFk",
                    "is_signedOut" =>1,
                    "number_of_orders" =>0,
                    "total_items_sent" =>null,
                    "total_amount_spent" =>null,
                    "version_no" =>"1.3.4",
                    ],
            'address'=> [[
                "id" => 1337,
                "latitude" => 30.036899999999999266719896695576608180999755859375,
                "longitude" => 31.4284999999999996589394868351519107818603515625,
                "building_no" => "building_no_sameh",
                "street_address" => "street_addresswertugcf",
                "floor_no" => "012",
                "apartment_no" => "rrr",
                "address_type" => "home",
                "additional_directions" => "dddt",
                "is_default" => 1
                ]],
            "preference"=>[]
            
        );
        return json_encode($success_arr);
    }
   
    public function getOrders(Request $request){
        // $this->authenticate($request);

        $success_arr = array(
            "status" => 1,
            "message" => "Successfully retrieved orders against customer.",
            "orders" => [
                ["id"=>1812,
                    "total_price"=>null,
                    "pickup_date"=>"2020-01-19",
                    "pickup_start"=>"17:00:00",
                    "delivery_date"=>"2020-01-21",
                    "delivery_end"=>"19:00:00",
                    "created_at"=>"2020-01-19 14:41:23",
                    "status"=>"Order placed",
                    "product_count"=>null,
                    "is_rated"=>0
                ]
            ],
            "cancellation_fee" => 15,
            "cancellation_buffer" => 120,
            "pickupRescStartBuffer" => 300,
            "deliveryRescEndBuffer" => 300,
            "rescheduling_fee" => 10
        );
         
        return json_encode($success_arr, JSON_NUMERIC_CHECK);
 
    }


    public function getAvailablePromotions(Request $request) {
        // $this->authenticate($request);
          
        $success_arr = array(
            'status' => 1,
            'message' => 'Successfully promotions orders against customer.',
            'promotions'=> [[
                    'id' => 1,
                    'name' => "DMA30",
                    'type' => "fixed",
                    'discount_figure' => 30,
                    'cap' => 0,
                    'usage_limit' => 1,
                    'min_order' => 0,
                    'start_date' => "2019-07-20",
                    'end_date' => "2020-04-30",
                    'is_archive' => 0,
                    'created_at' => "2019-06-03 10:06:10",
                    'updated_at' => "2020-01-10 05:26:25",
                    'title' => "Launch promotion",
                    'message' => "Enjoy a fixed E£30 off your first Makwa",
                    'show_in_app' => 1
                ]]
        );
        return json_encode($success_arr, JSON_NUMERIC_CHECK);
    }


    public function getPersonalCode(Request $request) {

        // $this->authenticate($request);

        $success_arr = array(
            'status' => 1,
            'message' => 'Successfully promotions orders against customer.',
            'code'=>[
                'id' => 4759,
                'name' => "e3augQ",
                'customer_id' => 4906,
                'created_at' => "2020-01-15 20:48:57",
                'updated_at' => "2020-01-15 20:48:57",
                'discount_max' => 25
            ]
        );
        return json_encode($success_arr, JSON_NUMERIC_CHECK);
    }

    public function getAvailableSlots(Request $request)
    {
        // $this->authenticate($request);
        return '{
            "status": 1,
            "message": "Successfully retrieved time slots against location.",
            "buffer_time": 60,
            "turnaround_time": 2640,
            "pickup_time_slots": [
                {
                    "date": "Sun 19 Jan",
                    "slots": []
                },
                {
                    "date": "Mon 20 Jan",
                    "slots": []
                },
                {
                    "date": "Tue 21 Jan",
                    "slots": [
                        {
                            "slot": "12:00 PM - 02:00 PM",
                            "order_availability": 1
                        }
                    ]
                },
                {
                    "date": "Wed 22 Jan",
                    "slots": []
                },
                {
                    "date": "Thu 23 Jan",
                    "slots": []
                },
                {
                    "date": "Fri 24 Jan",
                    "slots": []
                },
                {
                    "date": "Sat 25 Jan",
                    "slots": []
                },
                {
                    "date": "Sun 26 Jan",
                    "slots": []
                },
                {
                    "date": "Mon 27 Jan",
                    "slots": []
                },
                {
                    "date": "Tue 28 Jan",
                    "slots": [
                        {
                            "slot": "12:00 PM - 02:00 PM",
                            "order_availability": 1
                        }
                    ]
                },
                {
                    "date": "Wed 29 Jan",
                    "slots": []
                },
                {
                    "date": "Thu 30 Jan",
                    "slots": []
                },
                {
                    "date": "Fri 31 Jan",
                    "slots": []
                },
                {
                    "date": "Sat 01 Feb",
                    "slots": []
                },
                {
                    "date": "Sun 02 Feb",
                    "slots": []
                },
                {
                    "date": "Mon 03 Feb",
                    "slots": []
                },
                {
                    "date": "Tue 04 Feb",
                    "slots": [
                        {
                            "slot": "12:00 PM - 02:00 PM",
                            "order_availability": 1
                        }
                    ]
                },
                {
                    "date": "Wed 05 Feb",
                    "slots": []
                },
                {
                    "date": "Thu 06 Feb",
                    "slots": []
                },
                {
                    "date": "Fri 07 Feb",
                    "slots": []
                },
                {
                    "date": "Sat 08 Feb",
                    "slots": []
                }
            ],
            "delivery_time_slots": [
                {
                    "date": "Sun 19 Jan",
                    "slots": []
                },
                {
                    "date": "Mon 20 Jan",
                    "slots": []
                },
                {
                    "date": "Tue 21 Jan",
                    "slots": []
                },
                {
                    "date": "Wed 22 Jan",
                    "slots": []
                },
                {
                    "date": "Thu 23 Jan",
                    "slots": [
                        {
                            "slot": "12:00 PM - 02:00 PM",
                            "order_availability": 1
                        }
                    ]
                },
                {
                    "date": "Fri 24 Jan",
                    "slots": []
                },
                {
                    "date": "Sat 25 Jan",
                    "slots": []
                },
                {
                    "date": "Sun 26 Jan",
                    "slots": []
                },
                {
                    "date": "Mon 27 Jan",
                    "slots": []
                },
                {
                    "date": "Tue 28 Jan",
                    "slots": []
                },
                {
                    "date": "Wed 29 Jan",
                    "slots": []
                },
                {
                    "date": "Thu 30 Jan",
                    "slots": [
                        {
                            "slot": "12:00 PM - 02:00 PM",
                            "order_availability": 1
                        }
                    ]
                },
                {
                    "date": "Fri 31 Jan",
                    "slots": []
                },
                {
                    "date": "Sat 01 Feb",
                    "slots": []
                },
                {
                    "date": "Sun 02 Feb",
                    "slots": []
                },
                {
                    "date": "Mon 03 Feb",
                    "slots": []
                },
                {
                    "date": "Tue 04 Feb",
                    "slots": []
                },
                {
                    "date": "Wed 05 Feb",
                    "slots": []
                },
                {
                    "date": "Thu 06 Feb",
                    "slots": [
                        {
                            "slot": "12:00 PM - 02:00 PM",
                            "order_availability": 1
                        }
                    ]
                },
                {
                    "date": "Fri 07 Feb",
                    "slots": []
                },
                {
                    "date": "Sat 08 Feb",
                    "slots": []
                }
            ]
        }';

        $bufferTime= GlobalSetting::select('value')->where('setting_name','buffer_time')->first();
        $turnaroundTime= GlobalSetting::select('value')->where('setting_name','turnaround_time')->first();
        if(!$request->has('address_id'))
        {
            $failure_arr = array(
                'status' => 0,
                'message' => 'Parameter Missing - location_id should not be empty.',
                            
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }
        else
        {
            $address =$request->input('address_id');

            $locationid = CustomerAddress::select('location_id')->where('id',$address)->first();

            if(empty($locationid))
            {
                $failure_arr = array(
                'status' => 0,
                'message' => 'No address with this address_id exists.',
                            
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
            $location=$locationid->location_id;
            
            $chkLocation= Location::select('id')->where('id',$location)->first();

            if(empty($chkLocation))
            {
                $failure_arr = array(
                    'status' => 1,
                    'message' => 'No Location found against this location ID.',
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
        }
        $maxOrders = GlobalSetting::select('value')->where('setting_name','max_orders')->first();

        $maxOrdersPerSlot=$maxOrders->value;

        if($request->has('day'))
        {
            $day=$request->input('day');
            $checkDay=TimeSlot::select('id')->where('day',"$day")->first();
            if(!empty($checkDay))
            {
                for($i=0;$i<=6;$i++)
                {
                    $curDay=date('l',strtotime('+'.$i.' days'));
                    if(ucwords($day) == $curDay)
                    {
                        $slotDay=date('D',strtotime('+'.$i.' days'));
                        $slotDate=date('d M',strtotime('+'.$i.' days'));
                        $pickupslots=DB::select( DB::raw("select CONCAT(TIME_FORMAT(time_slots.start_time, '%h:%i %p'),' - ', TIME_FORMAT(time_slots.end_time, '%h:%i %p')) as slot,
                                    IF(count(orders.id)>=".$maxOrdersPerSlot.", '0', '1')
                                    as order_availability from time_slots 
                                    left join orders on DAYNAME(orders.pickup_date) = time_slots.day and 
                                    TIME(orders.pickup_start) >= time_slots.start_time and TIME(orders.pickup_end)<time_slots.end_time
                                    and date(orders.pickup_date) >= CURDATE()
                                    where time_slots.location_id = ".$location." and time_slots.day ='".$day."' and
                                    time_slots.is_archive=0 and time_slots.type='pickup'
                                    group by time_slots.id,time_slots.start_time,time_slots.end_time,time_slots.day ") );
                        $deliveryslots=DB::select( DB::raw("select CONCAT(TIME_FORMAT(time_slots.start_time, '%h:%i %p'),' - ', TIME_FORMAT(time_slots.end_time, '%h:%i %p')) as slot,
                                    IF(count(orders.id)>=".$maxOrdersPerSlot.", '0', '1')
                                    as order_availability from time_slots  
                                    left join orders on DAYNAME(orders.delivery_date) = time_slots.day and 
                                    TIME(orders.delivery_start) >= time_slots.start_time and TIME(orders.delivery_end)<time_slots.end_time
                                    and date(orders.delivery_date) >= CURDATE()
                                    where time_slots.location_id = ".$location." and time_slots.day ='".$day."' and
                                    time_slots.is_archive=0 and time_slots.type='delivery'
                                    group by time_slots.id,time_slots.start_time,time_slots.end_time,time_slots.day ") );
                    }     
                }

                $success_arr = array(
                    'status' => 1,
                    'message' => 'Successfully retrieved time slots against location.',
                    'buffer_time'=>$bufferTime->value,
                    'turnaround_time'=>$turnaroundTime->value,
                    'date'=>$slotDay." ".$slotDate,
                    
                    'pickup_time_slots'=>$pickupslots,
                    'delivery_time_slots'=>$deliveryslots
                ); 
                            
            }
            else
            {
                $failure_arr = array(
                    'status' => 1,
                    'message' => 'No Data available for this day against this provided location.',
                    
                    
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
            
        }
        else
        {
            
            $pickupslots = array();
            $deliveryslots =array();
            $buffernew=$bufferTime->value;
            $bufferDate=date('Y-m-d', strtotime('+'.$buffernew.' minutes'));
            for($i=0;$i<=20;$i++)
            {
                $curDate=date('Y-m-d', strtotime('+'.$i.' days'));
                $slotDate=date('d M',strtotime($curDate));
                if($curDate >= $bufferDate)
                {
                    $curDay=date('l', strtotime($curDate));
                    $slotDay=date('D',strtotime($curDate));
                    if($curDate == $bufferDate)
                    {
                        $bufferTimechk=date('H:i:s', strtotime('+'.$buffernew.' minutes'));;

                        $pickup=DB::select( DB::raw("select * from (select CONCAT(TIME_FORMAT(time_slots.start_time, '%h:%i %p'),' - ', TIME_FORMAT(time_slots.end_time, '%h:%i %p')) as slot,
                            IF(count(orders.id)>=".$maxOrdersPerSlot.", '0', '1')
                            as order_availability from time_slots 
                            left join orders on DAYNAME(orders.pickup_date) = time_slots.day and 
                            TIME(orders.pickup_start) = time_slots.start_time and TIME(orders.pickup_end)=time_slots.end_time and orders.pickup_date='".$curDate."'
                            where time_slots.location_id = ".$location." and time_slots.is_archive=0
                            and time_slots.type='pickup' and time_slots.day='".$curDay."' and time_slots.start_time > '".$bufferTimechk."'
                            group by time_slots.id,time_slots.start_time,time_slots.end_time,time_slots.day) t where t.order_availability=1 ") );
                    }
                    else
                    {
                        $pickup=DB::select( DB::raw("select * from (select CONCAT(TIME_FORMAT(time_slots.start_time, '%h:%i %p'),' - ', TIME_FORMAT(time_slots.end_time, '%h:%i %p')) as slot,
                            IF(count(orders.id)>=".$maxOrdersPerSlot.", '0', '1')
                            as order_availability from time_slots 
                            left join orders on DAYNAME(orders.pickup_date) = time_slots.day and 
                            TIME(orders.pickup_start) = time_slots.start_time and TIME(orders.pickup_end)=time_slots.end_time and orders.pickup_date='".$curDate."'
                            where time_slots.location_id = ".$location." and time_slots.is_archive=0
                            and time_slots.type='pickup' and time_slots.day='".$curDay."'
                            group by time_slots.id,time_slots.start_time,time_slots.end_time,time_slots.day) t where t.order_availability=1 ") );
                            
                    }
                    
                    $delivery=DB::select( DB::raw("select * from (select CONCAT(TIME_FORMAT(time_slots.start_time, '%h:%i %p'),' - ', TIME_FORMAT(time_slots.end_time, '%h:%i %p')) as slot,
                            IF(count(orders.id)>=".$maxOrdersPerSlot.", '0', '1')
                            as order_availability from time_slots 
                            left join orders on DAYNAME(orders.delivery_date) = time_slots.day and 
                            TIME(orders.delivery_start) = time_slots.start_time and TIME(orders.delivery_end)=time_slots.end_time and orders.delivery_date='".$curDate."'
                            where time_slots.location_id = ".$location." and time_slots.is_archive=0
                            and time_slots.type='delivery' and time_slots.day='".$curDay."'
                            group by time_slots.id,time_slots.start_time,time_slots.end_time,time_slots.day) t where t.order_availability=1 ") );        

                    array_push($pickupslots,array(
                                'date'=>$slotDay." ".$slotDate,
                                'slots'=>$pickup
                            ));  
                    array_push($deliveryslots,array(
                            'date'=>$slotDay." ".$slotDate,
                            'slots'=>$delivery,
                        ));

                }
                
            }
            $success_arr = array(
                'status' => 1,
                'message' => 'Successfully retrieved time slots against location.',
                'buffer_time'=>$bufferTime->value,
                'turnaround_time'=>$turnaroundTime->value,
                'pickup_time_slots'=>$pickupslots,
                'delivery_time_slots'=>$deliveryslots
            );
            
        }
        
        return json_encode($success_arr, JSON_NUMERIC_CHECK);
        
        

    }

}
