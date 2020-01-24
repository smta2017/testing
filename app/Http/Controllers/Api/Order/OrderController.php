<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\GlobalSetting;
use App\Order;
use App\Promotion;
class OrderController extends Controller
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
        $newOrder->save();
       
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

    public function handelReturnResultFail($msg)
    {
        $failure_array=array(
            "status" => 0,
            "message" => $msg
            );
            return json_encode($failure_array,JSON_NUMERIC_CHECK);  
    }
}
