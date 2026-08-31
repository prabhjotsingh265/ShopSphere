@extends('admin.layouts.app')

@section('title')
    Orders
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fa-solid fa-cart-shopping"></i></div>
                    <h3>Orders <span class="count-pill">{{ $orders->count() }}</span></h3>
                </div>
            </div>
            <div class="card ss-panel">
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Product Price</th>
                                <th>By</th>
                                <th>Coupon</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Order Date</th>
                                <th>Delivered at</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $key => $order)
                                <tr>
                                    <td>{{ $key += 1 }}</td>
                                    <td>
                                        <span class="d-flex flex-column">
                                            @foreach ($order->products as $product)
                                                {{ $product->name }}
                                            @endforeach
                                        </span>
                                    </td>
                                    <td>
                                        <span class="d-flex flex-column mono">
                                            @foreach ($order->products as $product)
                                                ${{ $product->price }}
                                            @endforeach
                                        </span>
                                    </td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>
                                        @if ($order->coupon()->exists())
                                            <span class="badge bg-success">
                                                {{ $order->coupon->name }}
                                            </span>
                                        @else
                                           <span class="badge bg-danger">
                                                N/A
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $order->qty }}</td>
                                    <td class="mono">${{ $order->total }}</td>
                                    <td>{{ $order->created_at }}</td>
                                    <td>
                                        @if ($order->delivered_at)
                                            <span class="badge bg-success">
                                                {{ $order->delivered_at }}
                                            </span>
                                        @else
                                            <a href="{{route('admin.orders.update',$order->id)}}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-pencil"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" onclick="deleteItem({{$order->id}})" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form id="{{$order->id}}" action="{{route('admin.orders.delete',$order->id)}}" method="post">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    @if ($orders->isEmpty())
                        <div class="ss-empty">No orders yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
