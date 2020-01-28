@extends('layouts.admin.app')

@section('content')
<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Dashboard</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="index.html">Home</a></li>
                    <li><span>Orders</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            <div class="user-profile pull-right">
                <img class="avatar user-thumb" src="assets/images/author/avatar.png" alt="avatar">
                <h4 class="user-name dropdown-toggle" data-toggle="dropdown">Makwa User <i class="fa fa-angle-down"></i></h4>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">Message</a>
                    <a class="dropdown-item" href="#">Settings</a>
                    <a class="dropdown-item" href="#">Log Out</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- page title area end -->
<div class="main-content-inner">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice-area">
                        <div class="invoice-head">
                            <div class="row">
                                <div class="iv-left col-4">
                                    <span>Order - {{$order->id}} </span>
                                    <hr>  
                                    <ul class="invoice-date">
                                    <li>Delivery Date : {{$order->delivery_date}}</li>
                                    <li> <b> Sotr Time : {{\Carbon\Carbon::parse($order->delivery_start)->format('g:i')}} - {{\Carbon\Carbon::parse($order->delivery_end)->format('g:i A')}}</b></li>
                                </ul>
                                </div>
                               
                                <div class="iv-right col-4 text-md-right">
                                    <span>Customer #{{$order->Customer->id}} </span>
                                    <hr>  
                                    <div class="invoice-address">
                                        <h5>{{$order->Customer->first_name}} {{$order->Customer->last_name}}</h5>
                                    </div>
                                    <div class=" text-right">
                                        @if($order->OrderProducts->where('delivery_sorted',0)->count()==0)
                                        <form action="/confirm-delifery-sort" method="post">
                                        @csrf
                                        <input type="hidden" name="oid" value="{{$order->id}}">
                                            <button type="submit" class="btn btn-success">Confirm</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div class="row">
                                
                                @foreach($order->OrderProducts->groupBy('ProductPrice.service_id') as  $OrderProduct)
                                <div class="col-6">
                                    <h3>{{$OrderProduct[0]->ProductPrice->Service->name}}</h3>
                                    @foreach($OrderProduct->groupBy('product_price_id') as $OrderProd)
                                    <form action="/confirm-item" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_price_id" value="{{$OrderProd[0]->ProductPrice->id}}">
                                    <input type="hidden" name="oid" value="{{$OrderProd[0]->order_id}}">
                                    @if($OrderProd[0]->delivery_sorted)
                                    <span style="margin: 5px;" class="btn btn-success">{{$OrderProd[0]->ProductPrice->Product->name}} <span class="badge badge-pill badge-danger">{{$OrderProd->count()}}</span></span>
                                    @else
                                    <button class="btn @if($OrderProduct[0]->ProductPrice->Service->id==1) btn-primary @elseif($OrderProduct[0]->ProductPrice->Service->id==2) btn-dark @endif" style="margin: 5px ;font-size: 18px"> {{$OrderProd[0]->ProductPrice->Product->name}} <span class="badge badge-pill badge-danger">{{$OrderProd->count()}}</span></button>    
                                    @endif
                                </form>
                                @endforeach
                                </div>
                                @endforeach
                                    
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- main content area end -->
@endsection


@section('javascrip')
<script>
    let searchParams = new URLSearchParams(window.location.search);
    if (searchParams.has('servtype')) {
        if (searchParams.get('servtype') == 1) {
            iron_service();
        } else if (searchParams.get('servtype') == 2) {

            clean_service();
        }
    };

    // $('#serv-1').on("click", function(e) {
    //     e.preventDefault();
    //     iron_service();
    // });

    // $('#serv-2').on("click", function(e) {
    //     e.preventDefault();
    //     clean_service();
    // });

    function iron_service() {
        $('.service_id').val(1);
        $('.product-name').removeClass('btn-dark');
        $('.product-name').addClass('btn-primary');
        $('.invoice-table').css('display', 'block');
    }

    function clean_service() {
        // console.log(searchParams.get('servtype'));

        $('.service_id').val(2);
        $('.product-name').removeClass('btn-primary');
        $('.product-name').addClass('btn-dark');
        $('.invoice-table').css('display', 'block');
    }

    $('#producttable').DataTable({
        // responsive: true
        "info": false,
        "paging": false,
        "lengthChange": false,
        "order": [
            [0, "desc"]
        ]
    });

    $('#producttable2').DataTable({
        // responsive: true
        "info": false,
        "paging": false,
        "lengthChange": false,
        "order": [
            [0, "desc"]
        ]
    });
</script>
@endsection