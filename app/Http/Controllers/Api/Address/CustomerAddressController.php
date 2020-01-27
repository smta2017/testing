<?php

namespace App\Http\Controllers\Api\Address;

use App\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CustomerAddress;
use DB;
use JWTAuth;

class CustomerAddressController extends Controller
{
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
        // $request->validate([
        //     'order_id' => 'numeric|required|min:4|max:999998',
        //     'building_no'=>'max:99',
        //     'street_address'=>'max:255',
        //     'floor_no'=>'max:99',
        //     'apartment_no'=>'max:99',
        //     'additional_directions'=>'max:255',
        //     'latitude' => 'required|regex:^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$',
        //     'longitude' => 'required|regex:^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$',
        //     'location_id'=>'required|numeric',
        //     'customer_id'=>'required|numeric',
        //     'type_id'=>'required|numeric',
        // ]);

        $customerAddress=new CustomerAddress();
        if(isset($request['customer_id']) && isset($request['type_id']) && !empty($request['customer_id']) && !empty($request['type_id']))
        {
            $customerAddress->customer_id=$request['customer_id'];
            $customerAddress->type_id=$request['type_id'];
            $address=CustomerAddress::select('*')
            ->where('customer_id',$request['customer_id'])
            ->where('type_id',$request['type_id'])
            ->get();
        }
        else
        {
            if(!isset($request['customer_id']) || empty($request['customer_id']))
            {
                $msg="Parameter missing - Unable to save customer address due to missing customer_id.";
            }
            if(!isset($request['type_id']) || empty($request['type_id']))
            {
                $msg="Parameter missing - Unable to save customer address due to missing type_id.";
            }
            $failure_msg=array(
                "status" => 0,
                "message" => $msg
            );
            return response()->json(compact('failure_msg'),201);
        }
        if(isset($request['building_no']) && !empty($request['building_no']))
        {
            $customerAddress->building_no=$request['building_no'];
        }
        else
        {
            $failure_msg=array(
                "status" => 0,
                "message" => 'Parameter missing - Unable to save customer address due to missing building_no.'
            );
            return response()->json(compact('failure_msg'),201);
        }
        if(isset($request['street_address']) && !empty($request['street_address']))
        {
            $customerAddress->street_address=$request['street_address'];
        }
        else
        {
            $failure_msg=array(
                "status" => 0,
                "message" => 'Parameter missing - Unable to save customer address due to missing street_address.'
            );
            return response()->json(compact('failure_msg'),201);   
        }
        if(isset($request['floor_no']) && !empty($request['floor_no']))
        {
            $customerAddress->floor_no=$request['floor_no'];
        }
        
        if(isset($request['apartment_no']) && !empty($request['apartment_no']))
        {   
            $customerAddress->apartment_no=$request['apartment_no'];
        }
        
        if(isset($request['additional_directions']))
        {
            $customerAddress->additional_directions=$request['additional_directions'];
        }
        if(isset($request['latitude']) && !empty($request['latitude']))
        {
            $customerAddress->latitude=$request['latitude'];
        }
        else
        {
            $failure_msg=array(
                "status" => 0,
                "message" => 'Parameter missing - Unable to save customer address due to missing latitude.'
            );
            return response()->json(compact('failure_msg'),201);
        }
        if(isset($request['longitude']) && !empty($request['longitude']))
        {
            $customerAddress->longitude=$request['longitude'];
        }
        else
        {
            $failure_msg=array(
                "status" => 0,
                "message" => 'Parameter missing - Unable to save customer address due to missing longitude.'
            );
            return response()->json(compact('failure_msg'),201);
        }
        if(isset($request['location_id']) && !empty($request['location_id']))
        {
            $customerAddress->location_id=$request['location_id'];
        }
        else
        {
            $failure_msg=array(
                "status" => 0,
                "message" => 'Parameter missing - Unable to save customer address due to missing location_id.'
            );
            return response()->json(compact('failure_msg'),201);   
        }
        if($address->count() <= 0 && $request['type_id']== 1)
        {
            $customerAddress->is_default = 1;
        }
        else if($address->count() > 0)
        {
            DB::table('customer_address')->where('id', $address[0]['id'])->delete();
            $default= CustomerAddress::select('id','type_id')
                ->where('is_default',1)
                ->where('customer_id',$request['customer_id'])
                ->first();
            if(empty($default) && $request['type_id'] == 1)
            {
                $customerAddress->is_default = 1;
            }
            if(!empty($default) && $default->id == $address[0]['id'])
            {
                $customerAddress->is_default = 1;
            }
            
        }
        if($customerAddress->save())
        {
            if($address->count() > 0)
            {
                $success_msg=array(
                    "status" => 1,
                    "message" => "Customer Address updated Successfully."
                );
            }
            else
            {
                $success_msg=array(
                    "status" => 1,
                    "message" => "Customer Address saved Successfully."
                );
            }
            
             return json_encode($success_msg, JSON_NUMERIC_CHECK);
        }
        else
        {
            $failure_msg=array(
                "status" => 0,
                "message" => "Something went wrong. Unable to save customer Address."
            );
             return json_encode($failure_msg, JSON_NUMERIC_CHECK);
        }
    
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
    public function destroy(Request $request)
    {
         $deleteAddress= CustomerAddress::destroy($request['id']);
         if($deleteAddress == true)
         {
             $success_arr = array(
                 'status' => 1,
                 'message' => 'Address Deleted Successfully.',
             );
             return json_encode($success_arr, JSON_NUMERIC_CHECK);
         }
         else
         {
             $failure_arr = array(
                 'status' => 0,
                 'message' => 'No Address Found against this ID.',
             );
             return json_encode($failure_arr, JSON_NUMERIC_CHECK);
         }
    }

    // ============ Helper =================
    public function markDefaultAddress(Request $request)
    {
        $request->validate([
            'address_id' => 'numeric|required|min:4|max:999998',
            'customer_id'=>'numeric|required|min:4|max:999998'
        ]);

        $address_id=$request['address_id'];
        $customer_id=$request['customer_id'];
        
        $old_default_customer_address = CustomerAddress::where('customer_id',$customer_id)->where('is_default',1)->first();
        if (!empty($old_default_customer_address)) {
            $old_default_customer_address->is_default=0;
            $old_default_customer_address->save();
        }else{
            $failure_arr = array('status' => 0,'message' => 'Missing.',);
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }   


        $customer_address = CustomerAddress::find($address_id);
        if (!empty($customer_address)) {
            $customer_address->is_default=1;
            $customer_address->save();
        }else{
            $failure_arr = array('status' => 0,'message' => 'Missing.',);
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }   

        $success_arr = array(
            'status' => 1,
            'message' => 'Address marked as default successfully',
        );
        return json_encode($success_arr, JSON_NUMERIC_CHECK);
    }

    public function updateProfile(Request $request)
    {
        if(isset($request['id']) && !empty($request['id']))
        {
            $customer= Customer::where('id',$request['CustomerId'])->first();
        }
        else
        {
            $failure_arr = array(
                'status' => 0,
                'message' => 'Parameter Missing - id should not be empty.',
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }
        if(!empty($customer))
        {
            $customer= Customer::find($request['CustomerId']);
            $customer->first_name = $request->first_name ;
            $customer->last_name = $request->last_name ;
            $customer->phone = $request->phone ;
            $customer->email = $request->email ;
            $customer->save();
            $customer->jwt_token =JWTAuth::getToken()->get();
            $success_arr = array(
                'status' => 1,
                'message' => 'Customer Profile updated successfully.',
                'customer'=>$customer,
                'address'=>$customer->CustomerAddresses,
                'preference'=>$customer->CustomerPreferencies
            );
            return json_encode($success_arr, JSON_NUMERIC_CHECK);
        }
        else
        {
            $failure_arr = array(
                'status' => 0,
                'message' => 'No Customer found against this ID.',
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }
    }
 
}
