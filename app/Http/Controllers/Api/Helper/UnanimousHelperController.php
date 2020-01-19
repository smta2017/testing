<?php

namespace App\Http\Controllers\Api\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Customer;
use JWTAuth;

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





    
    public function getProfile($id = null,Request $request)
    {
        // $this->authenticate($request);
        
    //    $success_arr= '[{"status":1,"message":"Successfully retrieved customer profile.","customer":{"id":4904,"email":"smta0@yahoo.com","phone":"01274200778","country_code":"2","first_name":"sameh","last_name":"taha","jwt_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBpLm1ha3dhYXBwLmNvbVwvYXBpXC9yZWdpc3RlciIsImlhdCI6MTU3OTExMjUxMiwiZXhwIjoxNTc5MTE2MTEyLCJuYmYiOjE1NzkxMTI1MTIsImp0aSI6IlhTVkV1dGJ5TjZHMDN5WXMiLCJzdWIiOm51bGwsInBydiI6IjhiNDIyZTZmNjU3OTMyYjhhZWJjYjFiZjFlMzU2ZGQ3NmEzNjViZjIifQ.PsnYb61hmLFlzlOT1elC8aMfkzA3EKHcHrQcP1c0sFk","is_signedOut":1,"number_of_orders":0,"total_items_sent":null,"total_amount_spent":null,"version_no":"1.3.4"},"address":[],"preference":[]}"]';

    //     return json_decode($success_arr);
    
       //    return ($success_arr);
 
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
            'address'=>[],
            "preference"=>[]
            
        );
        return json_encode($success_arr);
    }
   
    public function getOrders(Request $request)
    {
        // $this->authenticate($request);

        $success_arr = array(
            "status" => 1,
            "message" => "Successfully retrieved orders against customer.",
            "orders" => [],
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


        // {"status":1,"message":"Successfully promotions orders against customer.","promotions":[{"id":1,"name":"DMA30","type":"fixed","discount_figure":30,"cap":0,"usage_limit":1,"min_order":0,"start_date":"2019-07-20","end_date":"2020-04-30","is_archive":0,"created_at":"2019-06-03 10:06:10","updated_at":"2020-01-10 05:26:25","title":"Launch promotion","message":"Enjoy a fixed E\u00a330 off your first Makwa","show_in_app":1}]}
       
        // status: 1
        // message: "Successfully promotions orders against customer."
        // promotions: [{id: 1, name: "DMA30", type: "fixed", discount_figure: 30, cap: 0, usage_limit: 1, min_order: 0,…}]
        // 0: {id: 1, name: "DMA30", type: "fixed", discount_figure: 30, cap: 0, usage_limit: 1, min_order: 0,…}
        // id: 1
        // name: "DMA30"
        // type: "fixed"
        // discount_figure: 30
        // cap: 0
        // usage_limit: 1
        // min_order: 0
        // start_date: "2019-07-20"
        // end_date: "2020-04-30"
        // is_archive: 0
        // created_at: "2019-06-03 10:06:10"
        // updated_at: "2020-01-10 05:26:25"
        // title: "Launch promotion"
        // message: "Enjoy a fixed E£30 off your first Makwa"
        // show_in_app: 1
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

}
