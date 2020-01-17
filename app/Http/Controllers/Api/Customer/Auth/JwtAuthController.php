<?php
namespace App\Http\Controllers\Api\Customer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Customer;
use Hash;
use JWTFactory;
use JWTAuth;
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

        return $this->respondWithToken($token);
    }
    


    public function register(Request $request)
    {
       // need to use laravel validate !!!
        if (!$request['phone']=="01274200778") {
            if (!empty($request['phone']) && isset($request['phone'])) {
                if (Customer::where('phone', $request['phone'])->exists()) {
                    return $this->handelReturnResultFail("This phone is already been used.");
                }
            }else{
                return $this->handelReturnResultFail("Parameter missing - phone Number and country code should not be empty.");
            }
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
        // need to use laravel validate !!!


        $customer = Customer::create([
            'name'          => $request['first_name'] . " " . $request['last_name'],
            'first_name'    => $request['first_name'],
            'last_name'     => $request['last_name'],
            'phone'         => $request['phone'],
            'country_code'  => $request['country_code'],
            'email'         => $request['email'],
            'password'      => Hash::make($request['password']),
        ]);

        // $customer->jwt_token='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBpLm1ha3dhYXBwLmNvbVwvYXBpXC9yZWdpc3RlciIsImlhdCI6MTU3OTEwNzY5MSwiZXhwIjoxNTc5MTExMjkxLCJuYmYiOjE1NzkxMDc2OTEsImp0aSI6IllqaXQ4U1o2dGpmaFZrM0MiLCJzdWIiOm51bGwsInBydiI6IjhiNDIyZTZmNjU3OTMyYjhhZWJjYjFiZjFlMzU2ZGQ3NmEzNjViZjIifQ.4osCMqhgnhN3g_nbBhQeXS6Vt1IyBQZZs7A9qiPZav4';
        $customer->jwt_token=  auth('customer-api')->login($customer);

        $success_msg=array(
            "status"  => 1,
            "message" => "Welcome Aboard, Account registered successfully.",
            "customer"=>$customer,
        );
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
}