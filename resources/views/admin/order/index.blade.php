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

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Orders Table</h4> 
                    <div class="data-tables datatable-dark">
                        <table id="orderTable" class="text-center">
                            <thead class="text-capitalize">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Pickup</th>
                                    <th>Delivery</th>
                                    <th>Control</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders->sortByDesc('id') as $order)
                                <tr style="background:  @if($order->status_id) #   @endif ">
                                    <th scope="row">%{{$order->id}}</th>
                                    <td>{{$order->Customer->first_name }} {{ $order->Customer->last_name}} #{{$order->Customer->id }}</td>
                                    <td>{{$order->OrderStatus->name}} </td>
                                    <td>{{$order->pickup_date}} <br> <b>{{\Carbon\Carbon::parse($order->pickup_start)->format('H:i') }} {{ \Carbon\Carbon::parse($order->pickup_end)->format('H:i') }}</b></td>
                                    <td>{{$order->delivery_date}} <br> <b>{{\Carbon\Carbon::parse($order->delivery_start)->format('H:i') }} {{ \Carbon\Carbon::parse($order->delivery_end)->format('H:i') }}</b></td>
                                    <td>
                                    @if($order->status_id==1)
                                        <a href="#" value="{{$order->status_id}}" id="from_agent" order_id="{{$order->id}}" user="{{$order->Customer->first_name }}  {{ $order->Customer->last_name}} #{{$order->Customer->id }}" class="change-status btn btn-primary">From Agent</a>
                                    @elseif($order->status_id==2)
                                        <a href="service-step2?id={{$order->id}}" id="picup_sort"  target="_blanck" class="change-status btn btn-danger">Pickup sort</a>
                                    @elseif($order->status_id==4)
                                        <a href="service-step3?id={{$order->id}}" id="delivery_sort" target="_blanck" class="change-status btn btn-success">Delivery Sort</a>
                                    @elseif($order->status_id==3)
                                        Complete
                                    @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dark table end -->
    </div>
</div>
</div>
<!-- main content area end -->



<!-- ================ MODALS ============== -->
<div class="modal fade bd-from-agent-modal-lg" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order From Agent Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <form action="/service-step1" method="post">
            <div class="modal-body">
                <span><i class="fa fa-user"></i> <h5 id="customer"></h5></span>
                <br>
                <br>
                @csrf

                    <input class="form-control" type="hidden" type="text" id="order_id" name="order_id">

                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"  name="customCheck1" class="custom-control-input" id="customCheck1">
                        <label class="custom-control-label" for="customCheck1">Iron</label>
                    </div>
                    <br>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="customCheck2" class="custom-control-input" id="customCheck2">
                        <label class="custom-control-label" for="customCheck2">Clean & Iron</label>
                    </div>
                    <br>
                    <div class="form-group" style="display: none">
                        <label for="ironbags" class="col-form-label">Iron Bags</label>
                        <input class="form-control" type="text" value="0" id="ironbags" name="ironbags">
                    </div>
                    <div class="form-group" style="display: none">
                        <label for="clean-ironbags" class="col-form-label">Clean & Iron Bags</label>
                        <input class="form-control" type="text" value="0" id="clean-ironbags" name="cleanironbags">
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
 

<!-- ================ MODALS ============== -->

@endsection


@section('javascrip')
<script>
     $('#orderTable').DataTable({
            // responsive: true
            "order": [[ 0, "desc" ]]
    });

    $("#from_agent").on('click',function(e){
        e.preventDefault();
        $('#order_id').val($(this).attr('order_id'));
        $('#customer').html($(this).attr('user'));
        $('.bd-from-agent-modal-lg').modal("show");
    });
     
    //======================================
    

    $("#customCheck1").on('click',function(e){
        // e.preventDefault();
        if($('#customCheck1').is(':checked')){
            $("#ironbags").closest('div').css('display','block');
        }
        else{
            $("#ironbags").val(0);
            $("#ironbags").closest('div').css('display','none');
        }
    });
    
    $("#customCheck2").on('click',function(e){

        if($('#customCheck2').is(':checked')){
            $("#clean-ironbags").closest('div').css('display','block');
        }else{
            $("#clean-ironbags").val(0);
            $("#clean-ironbags").closest('div').css('display','none');
        }
    });
    

</script>
@endsection
