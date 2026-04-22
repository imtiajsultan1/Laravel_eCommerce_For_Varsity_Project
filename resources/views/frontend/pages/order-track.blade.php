@extends('frontend.layouts.master')

@section('title','DIU SHOP || Order Track Page')

@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Order Track</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
<section class="tracking_box_area section_gap py-5">
    <div class="container">
        <div class="tracking_box_inner">
            <p>To track your order please enter your Order ID in the box below and press the "Track" button. This was given
                to you on your receipt and in the confirmation email you should have received.</p>
            <form class="row tracking_form my-4" action="{{route('product.track.order')}}" method="post" novalidate="novalidate">
              @csrf
                <div class="col-md-8 form-group">
                    <input type="text" class="form-control p-2"  name="order_number" placeholder="Enter your order number" value="{{old('order_number', $searchedOrderNumber ?? '')}}">
                    @error('order_number')
                        <span class="text-danger d-block mt-2">{{$message}}</span>
                    @enderror
                </div>
                <div class="col-md-8 form-group">
                    <button type="submit" value="submit" class="btn submit_btn">Track Order</button>
                </div>
            </form>

            @if(!empty($trackingMessage))
                <div class="alert alert-{{ $trackingMessageType === 'error' ? 'danger' : 'success' }}">
                    {{$trackingMessage}}
                </div>
            @endif

            @if(!empty($trackedOrder))
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h4 class="mb-3">Order Details</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p class="mb-2"><strong>Order Number:</strong> {{$trackedOrder->order_number}}</p>
                                <p class="mb-2"><strong>Status:</strong>
                                    @if($trackedOrder->status=='new')
                                        <span class="badge badge-primary">{{$trackedOrder->status}}</span>
                                    @elseif($trackedOrder->status=='process')
                                        <span class="badge badge-warning">{{$trackedOrder->status}}</span>
                                    @elseif($trackedOrder->status=='delivered')
                                        <span class="badge badge-success">{{$trackedOrder->status}}</span>
                                    @else
                                        <span class="badge badge-danger">{{$trackedOrder->status}}</span>
                                    @endif
                                </p>
                                <p class="mb-2"><strong>Placed On:</strong> {{$trackedOrder->created_at->format('d M Y, h:i A')}}</p>
                                <p class="mb-2"><strong>Payment:</strong> {{ strtoupper($trackedOrder->payment_method) }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-2"><strong>Name:</strong> {{$trackedOrder->first_name}} {{$trackedOrder->last_name}}</p>
                                <p class="mb-2"><strong>Email:</strong> {{$trackedOrder->email}}</p>
                                <p class="mb-2"><strong>Total:</strong> {{ Helper::currency($trackedOrder->total_amount) }}</p>
                                <p class="mb-2"><strong>Shipping:</strong> {{ Helper::currency(optional($trackedOrder->shipping)->price ?? 0) }}</p>
                            </div>
                        </div>

                        @if($trackedOrder->cart_info->count() > 0)
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trackedOrder->cart_info as $item)
                                            <tr>
                                                <td>{{optional($item->product)->title ?? 'Product removed'}}</td>
                                                <td>{{$item->quantity}}</td>
                                                <td>{{ Helper::currency($item->price) }}</td>
                                                <td>{{ Helper::currency($item->amount) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
