<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @foreach($order->OrderProducts->groupBy('ProductPrice.service_id') as  $OrderProduct)
    <table>
        <thead>
            <tr class="text-capitalize">
                <th >{{$OrderProduct[0]->ProductPrice->Service->name}}</th>
                <th >PREF:</th>
            </tr>
        </thead>
        <tbody>
            @foreach($OrderProduct->groupBy('product_price_id') as $OrderProd)
            <tr>
                <td>{{$OrderProd[0]->ProductPrice->id}} - {{$OrderProd[0]->ProductPrice->product->name}}</td>
                <td>{{\App\OrderFastPreference::where('product_id',$OrderProd[0]->ProductPrice->product->id)->where('order_id',$OrderProd[0]->order_id)->first()["preference"]}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach
</body>
</html>