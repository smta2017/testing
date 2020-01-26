<?php

namespace App\Http\Controllers\Api\Service;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product;
use App\Http\Resources\ProductPrice as ResourcesProductPrice;
use App\ProductPrice;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $services = Service::where('is_archive',0)->get();
            $success_arr = array(
                'status' => 1,
                'message' => 'Successfully retrieved services names.',
                'services'=>$services,
            );
            return json_encode($success_arr, JSON_NUMERIC_CHECK);
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
    public function show(Request $request)
    {
        
        // print_r($customer_id);
        // exit;
        
        // $this->authenticate($request);
        // $publicPath=public_path('product-images');
        
        if(!$request->has('customer_id'))
        {
            $failure_arr = array(
                'status' => 0,
                'message' => 'Parameter Missing - customer_id should not be empty.',
                            
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
                        

        }
        else
        {
            $customer_id=$request->input('customer_id');
            $customer=Customer::find($customer_id);
            if(empty($customer))
            {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'No Location found against this customer_id.',
                                
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }

        }
        if($request->has('service_id'))
        {
            $service_id=$request->input('service_id');
            $service=Service::select('id','name')
            ->where('id',$service_id)->first();
            // return $service->name;
            if(!empty($service))
            {
                $productDetails= Service::selectRaw('products.id as product_id,CONCAT(UCASE(LEFT(REPLACE(products.name,"_"," "), 1)), 
                    SUBSTRING(REPLACE(products.name,"_"," "), 2)) as product_name,
                    product_price.price as product_price,products.image as image')
                    ->leftJoin('product_price', 'services.id', 'product_price.service_id')
                    ->leftJoin('products', 'products.id', 'product_price.product_id')
                    ->where('services.id',$service->id)
                    ->orderBy('products.id','ASC')
                    ->get();
                for($i=0;$i< sizeof($productDetails);$i++)
                {
                    // print_r($productDetails[$i]['product_id']);
                    // exit;
                    
                    
                        $customer=Customer::where('id',$customer_id)->first();
                        
                        $preference=DB::select( DB::raw("select preference.type,preference.default_value,
                        customer_preference.preference as customer_preference from products 
                        left join preference on products.id = preference.product_id left join customer_preference
                        on products.id = customer_preference.product_id and 
                        customer_preference.customer_id = ".$customer_id." 
                        where products.id = ".$productDetails[$i]['product_id']." ") )[0];
                        $productDetails[$i]=array(
                            'id'=>$productDetails[$i]['product_id'],
                            'name'=>$productDetails[$i]['product_name'],
                            'price'=>$productDetails[$i]['product_price'],
                            'image'=>asset('images/product-images/'.$productDetails[$i]['image'].''),    
                            'preferences'=>$preference,
                        );
                        // return asset('images/product-images/'.$productDetails[$i]['image'].'');
                }
                // return $publicPath;
                $service_name=ucwords((str_replace("_"," ",$service->name)));
                $success_arr = array(
                    'status' => 1,
                    'message' => 'Successfully retrieved service details against customer ID.',
                    'service'=>$service_name,
                    'products'=>$productDetails
                    
                );
                return json_encode($success_arr, JSON_NUMERIC_CHECK);   
            }
                
            else
            {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'No Service found against this ID.',
                    
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
        }
        else
        {
            $services= Service::all();
            $j=0;
            foreach($services as $service)
            {
                
                $productDetails= Service::selectRaw('services.name as service_name,products.id as product_id,CONCAT(UCASE(LEFT(REPLACE(products.name,"_"," "), 1)), 
                    SUBSTRING(REPLACE(products.name,"_"," "), 2)) as product_name,
                    product_price.price as product_price,products.image as image,preference.default_value as default_value,
                    preference.type as preference_type')
                    ->leftJoin('product_price', 'services.id', 'product_price.service_id')
                    ->leftJoin('products', 'products.id', 'product_price.product_id')
                    ->leftJoin('preference', 'preference.product_id', 'products.id')
                    ->where('services.id',$service->id)
                    ->where('product_price.service_id',$service->id)
                    ->orderBy('products.id','ASC')
                    ->get();
                    // return $productDetails;
                    for($i=0;$i< sizeof($productDetails);$i++)
                    {
                        // print_r($productDetails[$i]['product_id']);
                        // exit;
                        

                            $customer=Customer::where('id',$customer_id)->first();
                            
                            $preference=DB::select( DB::raw("select preference.type,preference.default_value,
                            customer_preference.preference as customer_preference 
                            from products 
                            left join preference on products.id = preference.product_id 
                            left join customer_preference
                            on products.id = customer_preference.product_id and customer_preference.customer_id = ".$customer_id." 
                            where products.id = ".$productDetails[$i]['product_id']." ") )[0];
                            $productDetails[$i]=array(
                                'id'=>$productDetails[$i]['product_id'],
                                'name'=>$productDetails[$i]['product_name'],
                                'price'=>$productDetails[$i]['product_price'], 
                                'image'=>asset('images/product-images/'.$productDetails[$i]['image'].''),
                                'preferences'=>$preference,
                            );
                            // return $productDetails[$i]['image'];
                            // return asset('images/product-images/'.$productDetails[$i]['image'].'');
                            // exit;
                            // return json_encode($productDetails);
                            
                    }
                    
                    $serviceProductDetails[$j]=array(
                        'id'=>$service->id,
                        'name'=>$service->name,
                        'products'=>$productDetails
                    );
                    $j++;
                    // return $i;
                    
            }
            
                
                // $service_name=ucwords((str_replace("_"," ",$service->name)));
                $success_arr = array(
                    'status' => 1,
                    'message' => 'Successfully retrieved service details against customer ID.',
                    'service'=>$serviceProductDetails
                    
                );
                return json_encode($success_arr, JSON_NUMERIC_CHECK);
            // return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }
    }

    public function show_new(Request $request)
    {
        if(!$request->has('CustomerId'))
        {
            $failure_arr = array(
                'status' => 0,
                'message' => 'Parameter Missing - customer_id should not be empty.',
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }
        else
        {
            $request->validate([
                'customer_id' => 'numeric|required|min:4|max:999998',
            ]);

            $customer_id=$request->input('CustomerId');
            $customer=Customer::find($customer_id);
            if(empty($customer))
            {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'No Location found against this customer_id.',
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
        }

        if($request->has('service_id'))
        {
            $service_id=$request->input('service_id');
            if(!empty($service))
            {
                $ProductPrice = ProductPrice::where('service_id',$service_id)->get();
                $success_arr = array(
                    'status' => 1,
                    'message' => 'Successfully retrieved service details against customer ID.',
                    'service'=>  $ProductPrice[0]->Service->name,
                    'products'=> Product::collection($ProductPrice),
                );
                return json_encode($success_arr, JSON_NUMERIC_CHECK);   
            }
            else
            {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'No Service found against this ID.',
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
        }
        else
        {
            $ProductPrice = ProductPrice::all();
            $ProductPrice = ResourcesProductPrice::collection($ProductPrice->groupBy('service_id'));
            $success_arr = array(
                'status' => 1,
                'message' => 'Successfully retrieved service details against customer ID.',
                'service'=>$ProductPrice
            );
            return json_encode($success_arr, JSON_NUMERIC_CHECK);
        }
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
}
