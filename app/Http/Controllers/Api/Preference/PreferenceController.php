<?php

namespace App\Http\Controllers\Api\Preference;

use App\Customer;
use App\CustomerPreference;
use App\Http\Controllers\Controller;
use App\Preference;
use App\Product;
use Illuminate\Http\Request;
use DB;

class PreferenceController extends Controller
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



    // ================= Helpers ======================

    public function setPreference(Request $request)
    {
        $preference=json_decode($request->getContent(), true);
        foreach($preference as $preference)
        {
            $request['customer_id']=$preference['customer_id'];
            $request['product_id']=$preference['product_id'];
            $request['preference']=$preference['preference'];
        
            if(!isset($request['customer_id']) || empty($request['customer_id']))
            {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'Parameter Missing - customer_id should not be empty.',
                    
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
            if(!isset($request['product_id']) || empty($request['product_id']))
            {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'Parameter Missing - product_id should not be empty.',
                    
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
            if(!isset($request['preference']) || empty($request['preference']))
            {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'Parameter Missing - preference should not be empty.',
                    
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
            $customer= Customer::selectRaw('id,email,phone,country_code,first_name,last_name,jwt_token')
            ->where('id',$request['customer_id'])->first();
            if(empty($customer))
            {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'No Customer found against this customer_id.',
                    
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
            $product= Product::selectRaw('id')
            ->where('id',$request['product_id'])->first();
            if(empty($product))
            {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'No Product found against this product_id.',
                    
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
            $customerPreferenceOld=CustomerPreference::selectRaw('id')
            ->where('customer_id',$request['customer_id'])
            ->where('product_id',$request['product_id'])->first();
            
            $customerPreference=new CustomerPreference();
            $customerPreference->customer_id=$request['customer_id'];
            $customerPreference->product_id=$request['product_id'];
            $customerPreference->preference=$request['preference'];
            $fixedPreference=Preference::selectRaw('id')
            ->where('product_id',$request['product_id'])
            ->where('type','fixed')->first();

            if(!empty($fixedPreference))
            {
                $failure_arr=array(
                    "status" => 0,
                    "message" => "Product Preference cannot be changed as it is fixed."
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
            else
            {
                if($customerPreference->save())
                {
                    $preference=CustomerPreference::selectRaw('id')
                    ->where('customer_id',$request['customer_id'])
                    ->where('product_id',$request['product_id'])->first();
                    if(!empty($customerPreferenceOld))
                    {
                        DB::table('customer_preference')->where('id',  $customerPreferenceOld['id'])->delete();
                        $success_arr=array(
                            "status" => 1,
                            "message" => "Product Preference Updated Successfully."
                        );
                    }
                    else
                    {
                        $success_arr = array(
                            'status' => 1,
                            'message' => 'Product preference Added successfully.',
                        );
                    }
                }
                else
                {
                    $failure_arr = array(
                        'status' => 0,
                        'message' => "Something went wrong. Unable to save product preference.",
                    );
                    return json_encode($failure_arr, JSON_NUMERIC_CHECK);
                }
            }
        }
        if(isset($success_arr))
        {
            return json_encode($success_arr, JSON_NUMERIC_CHECK);
        }
        else
        {
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }
        
    }
}
