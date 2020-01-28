<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderProduct;
use App\OrderService;
use App\Product;
use App\ProductPrice;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return view('admin.order.index',compact('orders'));
    }

    public function servicestep1(Request $request)
    {
        // return ($request);
        if (!$request['ironbags']==0) {
            $orderService = new OrderService();
            $orderService->order_id=$request->order_id;
            $orderService->service_id=1;
            $orderService->bag_count=$request['ironbags'];
            $orderService->save();
         }

        if (!$request['cleanironbags']==0) {
            $orderService = new OrderService();
            $orderService->order_id=$request->order_id;
            $orderService->service_id=2;
            $orderService->bag_count=$request['cleanironbags'];
            $orderService->save();
         }
        

        if (!empty($orderService)){
            $order = order::find($request->order_id);
            $order->status_id=2;
            $order->save();
        };
        return  redirect("/orders");
    }

    public function getServicestep2(Request $request)
    {
        $order = order::find($request->id);
    
        if (!empty($order)) {
            if ($order->status_id!=2) {
                return "wrong step";
            }else{
                if ($request->has('servicetype')) {
                    $ProductPrices=ProductPrice::where('service_id',$request->servicetype)->orderBy('id')->get();
                }
                else{
                    $ProductPrices=[];
                }

                $OrderProducts = OrderProduct::leftJoin('product_price' , 'product_price.id' ,'order_products.product_price_id')
                                                ->where('product_price.service_id', $request->servicetype)
                                                ->where('order_id',$order->id)->get();
                return view('admin.facility.pickupsort',compact('order','ProductPrices','OrderProducts'));
            }
        }else{
            return "wrong data";
        }
    }

    public function addProductOrderItem(Request $request)
    {
        $OrderProduct = new OrderProduct();
        $OrderProduct->order_id=$request->oid;
        $OrderProduct->product_price_id=$request->ppid;
        $OrderProduct->save();
        return redirect('/service-step2?servicetype='.$OrderProduct->ProductPrice->Service->id.'&id='.$request->oid);
    }

    public function confirmPickupSort(Request $request)
    {
        // return $request->method();
        $order = Order::find($request->oid);
        $CountService1 = $order->OrderServices->count();
        $CountService2 = $order->OrderProducts->groupBy('ProductPrice.service_id')->count();

        if ($CountService1 == $CountService2) {
            $order->status_id=4;
            
            if ($order->save()) {
                return view('admin.facility.sortinvoice',compact('order'));
            }
        }
        else{
            return "<h1>Bags not confirmed</h1>";
        };
    }

    public function getServicestep3(Request $request)
    {
        $order = order::where('id',$request->id)->where('status_id',4)->first();
        if (!empty($order)) {
            return view('admin.facility.deliverysort',compact('order'));
        }
    }

    
    public function confirmitem(Request $request)
    {
        
        $OrderProduct = OrderProduct::where('product_price_id', $request->product_price_id)
                                    ->where('order_id',$request->oid)
                                    ->update(['delivery_sorted' => '1']);
                                    return redirect('/service-step3?id='.$request->oid);
    }

    public function confirmDeliverySort(Request $request)
    {
        // return $request->method();
        $orders = Order::find($request->oid);
        
            $orders->status_id=3;
            $orders->save();
            return redirect('/orders');
      
    }
    
    public function resetSort(Request $request){
        OrderProduct::where('product_price_id',$request->opid)->delete();
        return redirect('/service-step2?servicetype='. $request->svid .'&id='.$request->oid);
    }

}
