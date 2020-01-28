<?php

namespace App\Http\Controllers\Api\Order;

use App\Customer;
use App\CustomerAddress;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\GlobalSetting;
use App\Http\Resources\CustomerAddress as ResourcesCustomerAddress;
use App\Order;
use App\OrderProduct;
use App\Http\Resources\OrderFastPreference as ResourcesOrderFastPreference;
use App\Http\Resources\ProductDetail;
use App\OrderFastPreference;
use App\Promotion;
use App\Service;
use Carbon\Carbon;
use DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
    }

    public function indextest()
    {
        return json_encode(array('status' => 0), JSON_NUMERIC_CHECK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->getMethod()=='OPTIONS') {return json_encode(array('status' => 0), JSON_NUMERIC_CHECK);}
        
        $deliveryFee = GlobalSetting::select('value')->where('setting_name','delivery_fee')->first();
        $serviceFee = GlobalSetting::select('value')->where('setting_name','service_fee')->first();
        $tax = GlobalSetting::select('value')->where('setting_name','tax')->first();

        $order=json_decode($request->getContent(),true);

        $newOrder= new Order();

        if(!isset($request['CustomerId']) || empty($request['CustomerId']))
        {
            return $this->handelReturnResultFail("Parameter missing - customer should not be empty.");
        }else{
            $newOrder->customer_id=$request['CustomerId'];
        }
       
        if(!isset($order['address_id']) || empty($order['address_id']))
        {
            return $this->handelReturnResultFail("Parameter missing -  address should not be empty.");
        }else{
            $newOrder->address_id=$order['address_id'];
        }
        
        if(isset($order['total_price']) && !empty($order['total_price']))
        {
            $newOrder->total_price=$order['total_price'];
        }
       
        $newOrder->delivery_fee = $deliveryFee->value;
        $newOrder->service_fee = $serviceFee->value;
        $newOrder->tax_amount = $tax->value;
        
        if(!isset($order['method_of_payment']) || empty($order['method_of_payment']))
        {
            return $this->handelReturnResultFail("Parameter missing - payment method should not be empty.");
        }
        else{
            $newOrder->method_of_payment=$order['method_of_payment'];
        }

        if(!isset($order['pickup_date']) || empty($order['pickup_date']))
        {
            return $this->handelReturnResultFail("Parameter missing -  pickup date should not be empty.");
        }
        else{
            $d=date('Y-m-d',strtotime($order['pickup_date']));
            $newOrder->pickup_date=$d;
            $newOrder->pickupdate=$order['pickup_date'];
        }

        if(!isset($order['pickup_start']) || empty($order['pickup_start']))
        {
            return $this->handelReturnResultFail("Parameter missing -  pickup start should not be empty.");
        }
        else{
            $newOrder->pickup_start=$order['pickup_start'];
        }

        if(!isset($order['pickup_end']) || empty($order['pickup_end']))
        {
            return $this->handelReturnResultFail("Parameter missing -  pickup_end should not be empty.");
        }
        else{
            $newOrder->pickup_end=$order['pickup_end'];
        }

        if(!isset($order['delivery_date']) || empty($order['delivery_date']))
        {
            return $this->handelReturnResultFail("Parameter missing -  delivery_date should not be empty.");
        }
        else{
            $d=date('Y-m-d',strtotime($order['delivery_date']));
            $newOrder->delivery_date=$d;
            $newOrder->deliverydate=$order['delivery_date'];
        }

        if(!isset($order['delivery_start']) || empty($order['delivery_start']))
        {
            return $this->handelReturnResultFail("Parameter missing -  delivery_start should not be empty.");
        }
        else{
            $newOrder->delivery_start=$order['delivery_start'];
        }

        if(!isset($order['delivery_end']) || empty($order['delivery_end']))
        {
            return $this->handelReturnResultFail("Parameter missing -  delivery_end should not be empty.");
        }
        else{
            $newOrder->delivery_end=$order['delivery_end'];
        }

        // ====================================================================================
        // ====================================================================================
        // ====================================================================================
        // ====================================================================================
        // ====================================================================================
        // ====================================================================================
        // ====================================================================================
        // ====================================================================================
        // ====================================================================================
        // ====================================================================================
        // ====================================================================================
        // ====================================================================================


        if(isset($order['promo_code']) && !empty($order['promo_code']))
        {
            $promotion=Promotion::selectRaw('id,type,discount_figure,cap,start_date,end_date,usage_limit,min_order')
                ->where('name',$order['promo_code'])
                ->where('start_date','<=',$order['pickup_date'])
                ->where('end_date','>=',$order['pickup_date'])
                ->where('is_archive',0)
                ->first();
            if(!$promotion == null )
            {
                $promoUsed=Order::selectRaw('count(id) as count')
                    ->where('customer_id',$request['CustomerId'])
                    ->where('promotion_id',$promotion->id)
                    ->first();
                if($promotion->usage_limit == 0)
                {
                    $newOrder->promotion_id=$promotion->id;
                }
                else if($promotion->usage_limit - $promoUsed->count >= 0 )
                {
                    $newOrder->promotion_id=$promotion->id;
                }
            }
        }

        $newOrder->status_id = 1;

        // $newOrder->created_at=date('Y-m-d H:i:s');
        if($newOrder->save()){
            if($order['customised_preference'] == 1)
            {
                foreach($order['preferences'] as $preferences)
                {
                    $orderPreference=new OrderFastPreference();
                    $orderPreference->order_id=$newOrder->id;
                    $orderPreference->product_id=$preferences['product_id'];
                    $orderPreference->preference=$preferences['preference'];
                    $orderPreference->name=$preferences['name'];
                    $orderPreference->save();
                }
            }
        };
       
        $success_arr = array(
            'status' => 1,
            'message' => 'Successfully placed this order.',
            'order_id'=>$newOrder->id,
            'pickup_date'=>$order['pickup_date'],
            'pickup_start_time'=>$order['pickup_start'],
            'pickup_end_time'=>$order['pickup_end'],
            'delivery_date'=>$order['delivery_date'],
            'delivery_start_time'=>$order['delivery_start'],
            'delivery_end_time'=>$order['delivery_end'],
            
        );
        return json_encode($success_arr, JSON_NUMERIC_CHECK);

        if($newOrder->save())
        {
            if(isset($order['promo_code']) && !empty($order['promo_code']))
            {
                $this->wr->applyCodeIfAllowed($order['promo_code'], $request['CustomerId'], $newOrder->id);
                $newOrder->refresh();
            }

            if (true) {
                $this->wr->applyDiscountIfAvailable($request['CustomerId'], $newOrder->id);
                $newOrder->refresh();
            }

            $chkBag=CustomerBag::select('id')
                ->where('customer_id',$request['CustomerId'])
                ->first();
                
            if(empty($chkBag))
            {
                $services=Service::all();
                foreach($services as $service)
                {
                    $customerBag= new CustomerBag();
                    $customerBag->customer_id=$request['CustomerId'];
                    $customerBag->bag_id='C'.$request['CustomerId'].'S'.$service->id;
                    $customerBag->service_id=$service->id;
                    $customerBag->qr_code=$request['CustomerId'].'-'.$service->id.'-'.date('Y-m-d').'.png';

                    QrCode::size(500)
                    ->format('png')
                    ->generate(''.$base.'/api/read-qr-code/?customer_id='.$request['CustomerId'].'&service_id='.$service->id.'', public_path('images/qr-codes/'.$request['CustomerId'].'-'.$service->id.'-'.date('Y-m-d').'.png'));
                    $customerBag->save();
                }
            }
            if($order['fast_checkout'] == 1)
            {
                $services=Service::all();
                foreach($services as $service)
                {
                        $bagId=CustomerBag::select('bag_id')
                        ->where('customer_id',$request['CustomerId'])
                        ->where('service_id',$service->id)
                        ->first();
                    $bagDetails= new BagDetail();
                    $bagDetails->order_id=$newOrder->id;
                    $bagDetails->bag_id=$bagId->bag_id;
                    // $bagDetails->user_id=$request['user_id'];
                    
                    $bagDetails->status_id=1;
                    $bagDetails->save();
                }
                foreach($order['services'] as $services)
                {
                    $orderFast=new OrderFast();
                    $orderFast->service_id=$services['id'];
                    $orderFast->order_id=$newOrder->id;
                    $orderFast->save();
                    
                    

                }

            
                if($order['customised_preference'] == 1)
                {
                    
                        foreach($order['preferences'] as $preferences)
                    {
                        $orderPreference=new OrderFastPreference();
                        $orderPreference->order_id=$newOrder->id;
                        $orderPreference->product_id=$preferences['product_id'];
                        $orderPreference->preference=$preferences['preference'];
                        $orderPreference->name=$preferences['name'];
                        $orderPreference->save();
                    }
                }
                $success_arr = array(
                    'status' => 1,
                    'message' => "Order placed successfully.",
                );
                return json_encode( $success_arr,JSON_NUMERIC_CHECK);
            } 
                        // return $orderBag;    
            
                
            // return $newOrder->id;
            foreach($order['products'] as $products)
            {
                $orderProduct= new OrderProduct;
                $orderProduct->order_id=$newOrder->id;
                if(!isset($products['service_id']) || empty($products['service_id']))
                {
                    $failure_array=array(
                        "status" => 0,
                        "message" => "Parameter missing -  service_id should not be empty."
                    );
                    return json_encode($failure_array,JSON_NUMERIC_CHECK);
                }
                else
                {
                    $chkService= Service::select('id')
                    ->where('id',$products['service_id'])
                    ->first();
                    if(empty($chkService))
                    {
                        $failure_array=array(
                            "status" => 0,
                            "message" => "No Service found against this ID."
                        );
                        return json_encode($failure_array,JSON_NUMERIC_CHECK);
                    }
                    else
                    {
                        $orderProduct->service_id=$products['service_id'];
                        // return $order['address_id'];
                    }
                    
                }
                if(!isset($products['product_id']) || empty($products['product_id']))
                {
                    $failure_array=array(
                        "status" => 0,
                        "message" => "Parameter missing -  product_id should not be empty."
                    );
                    return json_encode($failure_array,JSON_NUMERIC_CHECK);
                }
                else
                {
                    $chkProduct= Product::select('id')
                    ->where('id',$products['product_id'])
                    ->first();
                    if(empty($chkProduct))
                    {
                        $failure_array=array(
                            "status" => 0,
                            "message" => "No Product found against this ID."
                        );
                        return json_encode($failure_array,JSON_NUMERIC_CHECK);
                    }
                    else
                    {
                        $orderProduct->product_id=$products['product_id'];
                        // return $order['address_id'];
                    }
                    
                }
                // $orderProduct->service_id=$products['service_id'];
                // $orderProduct->product_id=$products['product_id'];
                if(isset($products['preference']) && !empty($products['preference']))
                {
                    $orderProduct->preference=$products['preference'];
                }
                if(isset($products['product_name']) && !empty($products['product_name']))
                {
                    $orderProduct->product_name=$products['product_name'];
                }
                else
                {
                    $failure_array=array(
                        "status" => 0,
                        "message" => "Parameter missing -  product_name should not be empty."
                    );
                    return json_encode($failure_array,JSON_NUMERIC_CHECK);
                }
                // $orderProduct->product_name=$products['product_name'];
                if(isset($products['product_price']) && !empty($products['product_price']))
                {
                    $orderProduct->product_price=$products['product_price'];
                }
                else
                {
                    $failure_array=array(
                        "status" => 0,
                        "message" => "Parameter missing -  product_price should not be empty."
                    );
                    return json_encode($failure_array,JSON_NUMERIC_CHECK);
                }
                if(isset($products['count']) && !empty($products['count']))
                {
                    $orderProduct->product_count=$products['count'];
                }
                // $orderProduct->product_price=$products['product_price'];
                $orderProduct->save();
            }
        $services=Service::all();

        foreach($services as $service)
        {
            $orderBag=Order::selectRaw('orders.id,sum(order_products.product_count) as count,service_id')
                ->join('order_products','orders.id','order_products.order_id')
                ->where('orders.customer_id',$request['CustomerId'])
                ->where('order_products.order_id',$newOrder->id)
                ->where('order_products.service_id',$service->id)
                ->groupBy('order_products.service_id','orders.id')->first();
                // return "hey";
                // return $request['CustomerId']." ".$newOrder->id." ".$service->id;
                // exit;
                if(!empty($orderBag))
                {
                    $bagId=CustomerBag::select('bag_id')
                        ->where('customer_id',$request['CustomerId'])
                        ->where('service_id',$service->id)
                        ->first();
                        // return $bagId->bag_id;
                    $bagDetails= new BagDetail();
                    $bagDetails->order_id=$newOrder->id;
                    $bagDetails->bag_id=$bagId->bag_id;
            // $bagDetails->user_id=$request['user_id'];
                    $bagDetails->count=$orderBag->count;
                    $bagDetails->status_id=1;
                    $bagDetails->save();
                }
                // return $orderBag;    
        }
        // $orderBag=Order::selectRaw('orders.id,sum(order_products.product_count) as count,service_id')
        //         ->join('order_products','orders.id','order_products.order_id')
        //         ->where('customer_id',$request['CustomerId'])
        //         ->where('order_id',$newOrder->id)
        //         ->groupBy('order_products.service_id','orders.id')->get();
                // return $newOrder->id;




            $success_arr = array(
                'status' => 1,
                'message' => 'Successfully placed this order.',
                'order_id'=>$newOrder->id,
                'pickup_date'=>$order['pickup_date'],
                'pickup_start_time'=>$order['pickup_start'],
                'pickup_end_time'=>$order['pickup_end'],
                'delivery_date'=>$order['delivery_date'],
                'delivery_start_time'=>$order['delivery_start'],
                'delivery_end_time'=>$order['delivery_end'],
                
            );
            return json_encode($success_arr, JSON_NUMERIC_CHECK);
        }
        else
        {
            $failure_arr = array(
                'status' => 0,
                'message' => "It's not you, its us... Something went wrong, please try again later.",
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }
    }

    public function show_old(Request $request)
    {
        
        // $this->authenticate($request);
        $price=0;
        if(!$request->has('order_id'))
        {
            $failure_arr = array(
                'status' => 0,
                'message' => 'Parameter Missing - order_id should not be empty.',
                            
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
                        

        }
        else
        {
            $orderId =$request->input('order_id');
            $order= Order::select('id','address_id')->where('id',$orderId)->first();
            if(!empty($order))
            {
                $addressId=$order->address_id;
                // return $addressId;
                $order=Order::selectRaw('orders.id,CONCAT(UCASE(LEFT(REPLACE(order_statuses.name,"_"," "), 1)), 
                    SUBSTRING(REPLACE(order_statuses.name,"_"," "), 2)) as status,
                    orders.total_price,orders.delivery_fee,orders.tax_amount,orders.discount_amount,orders.promotion_discount,
                    orders.sub_total,orders.service_fee,orders.cancellation_fee,orders.reschedule_fee,orders.method_of_payment,orders.pickup_date,
                    orders.pickup_start,orders.pickup_end,orders.delivery_date,orders.delivery_start,
                    orders.delivery_end,orders.is_rated,orders.created_at')
                    ->leftJoin('order_statuses','order_statuses.id','orders.status_id')
                    ->where('orders.id',$orderId)->first();
                $address = CustomerAddress::selectRaw('customer_address.id as address_id,latitude,location_id,longitude,building_no,street_address,floor_no,apartment_no,
                    address_type.name as address_type,additional_directions,is_default')
                    ->Join('address_type', 'customer_address.type_id', 'address_type.id')
                    ->where('customer_address.id',$addressId)->first();
                    // return $address;
                $services= Service::all();
                $i=0;
                $orderDetails=array();
                $pricearr=array();
                foreach($services as $service)
                {
                    // return $orderId;
                    // return $service->id;
                    $query=DB::select('select products.name as product_name,
                    order_products.product_count,order_products.product_price,order_products.product_price*order_products.product_count as product_total
                    from orders left join order_products on order_products.order_id=orders.id 
                    left join services on services.id=order_products.service_id
                    left join products on order_products.product_id=products.id
                    where orders.id='.$orderId.' and order_products.service_id='.$service->id.' group by 
                    products.name,order_products.id,order_products.product_count,order_products.product_price');
                    $service_name=ucwords((str_replace("_"," ",$service->name)));
                    // return $query;
                    
                    if(!empty($query))
                    {
                        
                        $orderDetails[$i]=array(
                            'service'=>$service_name,
                            'products'=>$query
                        );
                        $i++;
                    }
                }
                $price=$query=DB::select('select orders.id,
                    sum(order_products.product_count *order_products.product_price) as price
                    from orders left join order_products on order_products.order_id=orders.id 
                    
                    where orders.id='.$orderId.' group by 
                    orders.id')[0];
                $price=$price->price;
                    $service_name=ucwords((str_replace("_"," ",$service->name)));
                    $orderPreference=OrderFastPreference::select('preference','product_id', 'name')
                    ->where('order_id',$orderId)
                    ->get();
                     
                $order_total = $price+$order->delivery_fee+$order->tax_amount-$order->discount_amount - $order->promotion_discount + $order->service_fee + $order->cancellation_fee + $order->reschedule_fee;
                if ($order_total < 0) {
                    $order_total = 0;
                }
                $cancellationBuffer = GlobalSetting::select('value')
                            ->where('setting_name','cancellation_buffer')
                            ->first();
                $cancellation = GlobalSetting::select('value')
                            ->where('setting_name','cancellation_fee')
                            ->first();
                $pickupStartBuffer = GlobalSetting::select('value')
                            ->where('setting_name','rescheduling_buffer_start')
                            ->first();
                $pickupEndBuffer = GlobalSetting::select('value')
                            ->where('setting_name','rescheduling_buffer_end')
                            ->first();
                $rescheduling_fee = GlobalSetting::select('value')
                            ->where('setting_name','rescheduling_fee')
                            ->first();
              
                            
                $cancellation=$cancellation->value;

                $order=array(
                    'order_id'=>$order->id,
                    'status'=>$order->status,
                    'address_id' => $addressId,
                    'total_price'=> $order_total,
                    'delivery_fee'=>$order->delivery_fee,
                    'tax_amount'=>$order->tax_amount,
                    'discount_amount'=>$order->discount_amount,
                    'promotion_discount'=>$order->promotion_discount,
                    'order_cancel'=>$order->cancellation_fee,
                    'order_reschedule'=>$order->reschedule_fee,
                    'sub_total'=>$price,
                    'service_fee'=>$order->service_fee,
                    'method_of_payment'=>$order->method_of_payment,
                    'pickup_date'=>$order->pickup_date,
                    'pickup_start'=>$order->pickup_start,
                    'pickup_end'=>$order->pickup_end,
                    'delivery_date'=>$order->delivery_date,
                    'delivery_start'=>$order->delivery_start,
                    'delivery_end'=>$order->delivery_end,
                    'is_rated'=>$order->is_rated,
                    'created_at'=>date('Y-m-d H:i:s',strtotime($order->created_at)),
                    'address'=>$address,
                    'details'=>$orderDetails,
                    'order_preference'=>$orderPreference,
                    'delivery_raw_end'=>$order->delivery_date.' '.$order->delivery_end ,
                    'pickup_raw_start'=>$order->pickup_date.' '.$order->pickup_start ,
                    'cancellation_fee'=>$cancellation,
                    'cancellation_buffer'=>$cancellationBuffer,
                    'pickupRescStartBuffer'=>$pickupStartBuffer->value,
                    'deliveryRescEndBuffer'=>$pickupEndBuffer->value,
                    'rescheduling_fee'=>$rescheduling_fee->value
                );

                $success_arr = array(
                    'status' => 1,
                    'message' => 'Successfully retrieved order details. _ OLD',
                    'order'=>$order,
                );
                
                return json_encode($success_arr, JSON_NUMERIC_CHECK);
            }
            else
            {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'No Order found against this ID.',
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        if(!$request->has('order_id')){
            $failure_arr = array(
                'status' => 0,
                'message' => 'Parameter Missing - order_id should not be empty.',
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }
        else
        {
            $request->validate([
                'order_id' => 'numeric|required|min:4|max:999998',
            ]);

            $orderId = $request->input('order_id');
            $order= Order::find($orderId);
            if(!empty($order))
            {
                $price= DB::select('select sum(product_price*product_count) as price from order_products where order_id = '. $orderId)[0]->price;
               
                $OrderProducts=$order->OrderProducts->groupBy('service_id');
                $orderDetails = ProductDetail::collection($OrderProducts);
                $orderPreference= ResourcesOrderFastPreference::collection($order->OrderFastPreferencies);
                $order_total = $price+$order->delivery_fee+$order->tax_amount-$order->discount_amount - $order->promotion_discount + $order->service_fee + $order->cancellation_fee + $order->reschedule_fee;

                if ($order_total < 0) {
                    $order_total = 0;
                }

                $cancellationBuffer = GlobalSetting::select('value')->where('setting_name','cancellation_buffer')->first();
                $cancellation = GlobalSetting::select('value')->where('setting_name','cancellation_fee')->first();
                $pickupStartBuffer = GlobalSetting::select('value')->where('setting_name','rescheduling_buffer_start')->first();
                $pickupEndBuffer = GlobalSetting::select('value')->where('setting_name','rescheduling_buffer_end')->first();
                $rescheduling_fee = GlobalSetting::select('value')->where('setting_name','rescheduling_fee')->first();
                $cancellation=$cancellation->value;
              
                $order=array(
                    'order_id'=>$order->id,
                    'status'=>$order->OrderStatus->name,
                    'address_id' => $order->address_id,
                    'total_price'=> $order_total,
                    'delivery_fee'=>$order->delivery_fee,
                    'tax_amount'=>$order->tax_amount,
                    'discount_amount'=>$order->discount_amount,
                    'promotion_discount'=>$order->promotion_discount,
                    'order_cancel'=>$order->cancellation_fee,
                    'order_reschedule'=>$order->reschedule_fee,
                    'sub_total'=>0,
                    'service_fee'=>$order->service_fee,
                    'method_of_payment'=>$order->method_of_payment,
                    'pickup_date'=>$order->pickup_date,
                    'pickup_start'=>$order->pickup_start,
                    'pickup_end'=>$order->pickup_end,
                    'delivery_date'=>$order->delivery_date,
                    'delivery_start'=>$order->delivery_start,
                    'delivery_end'=>$order->delivery_end,
                    'is_rated'=>$order->is_rated,
                    'created_at'=>date('Y-m-d H:i:s',strtotime($order->created_at)),
                    'address'=> new ResourcesCustomerAddress($order->CustomerAddress),
                    'details'=>$orderDetails,
                    'order_preference'=>$orderPreference,
                    'delivery_raw_end'=>$order->delivery_date.' '.$order->delivery_end ,
                    'pickup_raw_start'=>$order->pickup_date.' '.$order->pickup_start ,
                    'cancellation_fee'=>$cancellation,
                    'cancellation_buffer'=>$cancellationBuffer,
                    'pickupRescStartBuffer'=>$pickupStartBuffer->value,
                    'deliveryRescEndBuffer'=>$pickupEndBuffer->value,
                    'rescheduling_fee'=>$rescheduling_fee->value
                );
                $success_arr = array(
                    'status' => 1,
                    'message' => 'Successfully retrieved order details.',
                    'order'=>$order,
                );
                
                return json_encode($success_arr, JSON_NUMERIC_CHECK);
            }
            else
            {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'No Order found against this ID.',
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
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

    //====================== Helper ==========================

    public function cancelOrder(Request $request) {
        $request=json_decode($request->getContent(),true);
        $orderId=$request['id'];
        $order=Order::find($orderId);
        $order->canceled_at=  Carbon::now()->toDateTimeString();
        $order->save();
        $success_arr=array(
                "status" => 1,
                "message" => "Order has been cancelled."
            );
        return json_encode($success_arr,JSON_NUMERIC_CHECK);
    }

    public function handelReturnResultFail($msg)
    {
        $failure_array=array(
            "status" => 0,
            "message" => $msg
            );
            return json_encode($failure_array,JSON_NUMERIC_CHECK);  
    }

    
    public function rateOrder(Request $request)
    {
        if(!empty($request['customer_id']) && isset($request['customer_id']))
        {
            $customerId=$request['customer_id'];    
        }
        else
        {
            $failure_array=array(
                "status" => 0,
                "message" => "Parameter missing - customer_id should not be empty."
            );
            return json_encode($failure_array,JSON_NUMERIC_CHECK);
        }
        if(!empty($request['order_id']) && isset($request['order_id']))
        {
            $orderId=$request['order_id'];
            $order= Order::select('id')->where('id',$orderId)->first();
            if(empty($order))
            {
                $failure_array=array(
                    "status" => 0,
                    'message' => 'No Order found against this ID.',
                );
                return json_encode($failure_array,JSON_NUMERIC_CHECK);
            }    
        }
        else
        {
            $failure_array=array(
                "status" => 0,
                "message" => "Parameter missing - order_id should not be empty."
            );
            return json_encode($failure_array,JSON_NUMERIC_CHECK);
        }
        if(!empty($request['customer_id']) && isset($request['customer_id']))
        {
            $customerId=$request['customer_id'];
            $customer= Customer::select('id')->where('id',$customerId)->first();
            if(empty($customer))
            {
                $failure_array=array(
                    "status" => 0,
                    'message' => 'No Customer found against this ID.',
                );
                return json_encode($failure_array,JSON_NUMERIC_CHECK);
            }    
        }
        else
        {
            $failure_array=array(
                "status" => 0,
                "message" => "Parameter missing - customer_id should not be empty."
            );
            return json_encode($failure_array,JSON_NUMERIC_CHECK);
        }
        if(!empty($request['rating']) && isset($request['rating']))
        {
            $rating=$request['rating'];
            if (is_numeric($rating))
            {

            }   
            else
            {
                $failure_array=array(
                    "status" => 0,
                    "message" => "Parameter rating should be numeric."
                );
                return json_encode($failure_array,JSON_NUMERIC_CHECK);
            } 
        }
        else
        {
            $failure_array=array(
                "status" => 0,
                "message" => "Parameter missing - rating should not be empty."
            );
            return json_encode($failure_array,JSON_NUMERIC_CHECK);
        }
        $customerOrder= Order::select('id')
                ->where('id',$orderId)
                ->where('customer_id',$customerId)
                ->first();
        if(empty($customerOrder))
        {
            $failure_arr = array(
                'status' => 0,
                'message' => 'No Such Order exists against this customer.',
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }
        else
        {
            $customerOrder->rating=$rating;
            $customerOrder->is_rated=1;
            if(isset($request['comment']) && !empty($request['comment']))
            {
                $customerOrder->comments=$request['comment'];
            }
            $customerOrder->update();
            $success_arr = array(
                'status' => 1,
                'message' => 'Order rating has been saved successfully',
            );
            return json_encode($success_arr, JSON_NUMERIC_CHECK);
        }
    }
   
    public function reschedule(Request $request)
    {
        $request=json_decode($request->getContent(),true);

        $order=Order::find($request["order_id"]);
       
        if(isset($request["pickup_date"]) && isset($request["pickup_start"])){
            $d=date('Y-m-d',strtotime($request["pickup_date"]));
            
            $order->pickup_date=$d;
            $order->pickupdate=$request["pickup_date"];
            
            $order->pickup_end=$request["pickup_end"];
           
        }
        if(!isset($request["delivery_date"]) || empty($request["delivery_date"]))
        {
            $failure_array=array(
                "status" => 0,
                "message" => "missing"
            );
            return json_encode($failure_array,JSON_NUMERIC_CHECK);
        }
        else
        {
            $d=date('Y-m-d',strtotime($request["delivery_date"]));
            $order->delivery_date=$d;
            $order->deliverydate=$request["delivery_date"];
        }
        if(!isset($request["delivery_start"]) || empty($request["delivery_start"]))
        {
            $failure_array=array(
                "status" => 0,
                "message" => "missing"
            );
            return json_encode($failure_array,JSON_NUMERIC_CHECK);
        }
        else
        {
            $order->delivery_start=$request["delivery_start"];
        }
        if(!isset($request["delivery_end"]) || empty($request["delivery_end"]))
        {
            $failure_array=array(
                "status" => 0,
                "message" => "missing"
            );
            return json_encode($failure_array,JSON_NUMERIC_CHECK);
        }
        else
        {
            $order->delivery_end=$request["delivery_end"];
        }
        
        $order->reschedule_fee = $order->reschedule_fee + $request["reschedule_fee"];
        $order->update();
        $success_arr=array(
                "status" => 1,
                "message" => "Order has been rescheduled."
            );
            return json_encode($success_arr,JSON_NUMERIC_CHECK);
        
    } 
}
