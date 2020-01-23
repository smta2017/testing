<?php

namespace App\Http\Controllers\Api\TimeStlot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\GlobalSetting;
use App\CustomerAddress;
use App\Location;
use App\TimeSlot;
use DB;


class TimeSlotController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $dd='dd';
        // $this->middleware('JwtClientAuth');
        // $this->middleware('auth:customer-api');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    //============================================================================================
    
    public function getAvailableSlots(Request $request)
    {
        if ($request->getMethod()=='OPTIONS') {return json_encode(array('status' => 0), JSON_NUMERIC_CHECK);}

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
