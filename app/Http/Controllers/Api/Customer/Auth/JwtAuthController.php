<?php
namespace App\Http\Controllers\Api\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Helper\EmailHelperController;
use Illuminate\Http\Request;
use App\Customer;
use App\ResetPassword;
use Hash;
use Mail;
use DB;

class JwtAuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:customer-api', ['except' => ['login','register']]);
    }
    

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        if (! $token = auth('customer-api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $customer =  $this->guard()->user();
        $customer->jwt_token = $token;
        return response()->json([
            "status" => 1,
            "message" => "Customer Logged In Successfully.",
            'access_token' => $token,
            'customer'=> $customer,
            'token_type' => 'bearer',
            'expires_in' => auth('customer-api')->factory()->getTTL() * 60
        ]);
        // return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        // need to use laravel validate !!!
        if (!empty($request['phone']) && isset($request['phone'])) {
            if (!$request['phone']=="01274200778") { //for testing only
                if (Customer::where('phone', $request['phone'])->exists()) {
                    return $this->handelReturnResultFail("This phone is already been used.");
                }
            }
        }else{
            return $this->handelReturnResultFail("Parameter missing - phone Number and country code should not be empty.");
        }
    
        if (!empty($request['email']) && isset($request['email'])) {
            if (Customer::where('email', $request['email'])->exists()) {
                        return $this->handelReturnResultFail("This Email is already been used.");
            }
        }else{
            return $this->handelReturnResultFail("Parameter missing - email Number and country code should not be empty.");
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
        ]);

        $customer->jwt_token=  auth('customer-api')->login($customer);

        $success_msg=array(
            "status"  => 1,
            "message" => "Welcome Aboard, Account registered successfully.",
            "customer"=>$customer,
        );
        
        $email_helper =  EmailHelperController::sendWelcomeEmail($customer->email);
        
        return json_encode($success_msg);
        // return redirect()->intended('login/customer');
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('customer-api')->user());
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('customer-api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('customer-api')->refresh());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'user' => $this->guard()->user(),
            'token_type' => 'bearer',
            'expires_in' => auth('customer-api')->factory()->getTTL() * 60
        ]);
    }

    public function guard() {
        return \Auth::Guard('customer-api');
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

    public function forgotPassword(Request $request)
    {
        $customer=Customer::select('id','first_name','email','jwt_token')
            ->where('email',$request['email'])
            ->first();
        
        
        if(empty($customer))
        {
            $failure_msg=array(
                "status" => 0,
                "message" => "No customer found against this email.",
            );
            return json_encode($failure_msg,JSON_NUMERIC_CHECK);    
        }
        $deletePasswordReset=ResetPassword::where('email',$request['email'])->delete();
        $passwordReset=new ResetPassword();
        $passwordReset->email=$request->email;
        $passwordReset->token=md5($customer->first_name.$customer->email.$customer->jwt_token.date('Y-m-d H:i:s'));
        $passwordReset->created_at=date('Y-m-d H:i:s');
        $passwordReset->save();
        $customer->token=md5($customer->first_name.$customer->email.$customer->jwt_token.date('Y-m-d H:i:s'));
        $data = array (
                    'customer'=>$customer,
                );
        Mail::send ( 'emails.passwordreset', $data, function ($message) use($customer) {
            $message->from ( 'noreply@makwaapp.com', 'Makwa' );
            $message->to ( $customer->email )->subject ( 'Password Reset Email' );
        } );

        $success_msg=array(
                "status" => 1,
                "message" => "Email has been sent.",
                "customer"=>$customer,
        );

        return json_encode($success_msg);
    }

    public function resetPassword($token)
    {
            $customer=ResetPassword::select(DB::raw('email'))
                    ->where('token',$token)->first();
            if(empty($customer))
            {
                return abort(404);
            }
            else
            {
                return view('resetpassword',['email'=>$customer->email]);
            }
    }
}