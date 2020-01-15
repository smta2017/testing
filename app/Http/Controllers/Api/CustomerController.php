<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Customer;
use DB;
class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:customer')->except(['store']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //  return view('customer');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->validator($request->all())->validate();
        // request()->validate([
        //     'first_name' =>'required|string|max:255',
        //     'last_name' => 'required|string|max:255',
        //     'email' => 'required|string|email|unique:customers|max:255',
        //     'password' => 'required|string|min:6',
        //     'phone' => 'required|unique:customers|max:20',
        //     'country_code' => 'required'
        // ]);

        // [
        //     'first_name.required'   => ["status" => 0,"message" => "Parameter missing - First Name should not be empty."],
        //     'last_name.required'    => ["status" => 0,"message" => "Parameter missing - Last Name should not be empty."],
        //     'phone.required'        => ["status" => 0,"message" => "Parameter missing - Phone Number and country code should not be empty."],
        //     'phone.unique'          => ["status" => 0,"message" => "This phone number is already been used."],
        //     'email.required'        => ["status" => 0,"message" => "Parameter missing - email Number and country code should not be empty."],
        //     'email.unique'          => ["status" => 0,"message" => "This email number is already been used."],
        //     'password.required'     => ["status" => 0,"message" => "Parameter missing - password Number and country code should not be empty."],
        //     'country_code.required' => ["status" => 0,"message" => "Parameter missing - country_code Number and country code should not be empty."],

        // ]
        if (!$request['phone']=="01274200778") {
            if (!empty($request['phone']) && isset($request['phone'])) {
                if (Customer::where('phone', $request['phone'])->exists()) {
                    return $this->handelReturnResultFail("This phone is already been used.");
                }
            }else{
                return $this->handelReturnResultFail("Parameter missing - phone Number and country code should not be empty.");
            }
        }
        if (!$request['email']=="smta0@yahoo.com") {
            if (!empty($request['email']) && isset($request['email'])) {
                if (Customer::where('email', $request['email'])->exists()) {
                    return $this->handelReturnResultFail("This Email is already been used.");
                }
            }else{
                return $this->handelReturnResultFail("Parameter missing - email Number and country code should not be empty.");
            }
        }
        if (empty($request['password']) && !isset($request['password'])) {
            return $this->handelReturnResultFail("Parameter missing - password Number and country code should not be empty.");
        }

        $customer = Customer::create([
            'name'          => $request['first_name'] . " " . $request['last_name'],
            'first_name'    => $request['first_name'],
            'last_name'     => $request['last_name'],
            'phone'         => $request['phone'],
            'country_code'  => $request['country_code'],
            'email'         => $request['email'],
            'password'      => Hash::make($request['password']),
            'jwt_token'      =>'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBpLm1ha3dhYXBwLmNvbVwvYXBpXC9yZWdpc3RlciIsImlhdCI6MTU3OTEwNzY5MSwiZXhwIjoxNTc5MTExMjkxLCJuYmYiOjE1NzkxMDc2OTEsImp0aSI6IllqaXQ4U1o2dGpmaFZrM0MiLCJzdWIiOm51bGwsInBydiI6IjhiNDIyZTZmNjU3OTMyYjhhZWJjYjFiZjFlMzU2ZGQ3NmEzNjViZjIifQ.4osCMqhgnhN3g_nbBhQeXS6Vt1IyBQZZs7A9qiPZav4',
        ]);

        $customerv=Customer::select('id','email','phone','country_code','first_name','last_name','jwt_token')
        ->where('id',$customer->id)
        ->first();
        // $customer->jwt_token='test';
        $success_msg=array(
            "status"  => 1,
            "message" => "Welcome Aboard, Account registered successfully.",
            "customer"=>$customerv,
        );
        return json_encode($success_msg);

        // return redirect()->intended('login/customer');
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




    // ======================================== Helper ==============================



    public function handelReturnResultFail($msg)
    {
        $failure_array=array(
            "status" => 0,
            "message" => $msg
            );
            return json_encode($failure_array,JSON_NUMERIC_CHECK);  
    }

   
}
