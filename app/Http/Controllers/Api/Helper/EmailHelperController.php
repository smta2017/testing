<?php

namespace App\Http\Controllers\Api\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Customer;
use Mail;
class EmailHelperController extends Controller
{ 
    public static function sendWelcomeEmail($email)
    {
        $customer=Customer::where('email',$email)->first();
        if(empty($customer))
        {
            $failure_msg=array(
                "status" => 0,
                "message" => "No customer found against this email.",
            );
            return json_encode($failure_msg,JSON_NUMERIC_CHECK);    
                // return $customer->email;
        }
        $data = array ();
       $mail =  Mail::send ( 'emails.welcome', $data, function ($message) use($customer) {
            
            $message->from ( 'donotreply@demo.com', 'Makwa' );
            
            $message->to ( $customer->email )->subject ( 'Welcome to Makwa' );
        } );
        $success_msg=array(
                "status" => 1,
                "message" => "Email Sent.",
                "customer"=>$customer,
               
            );
        // return json_encode($success_msg);
    }
}
