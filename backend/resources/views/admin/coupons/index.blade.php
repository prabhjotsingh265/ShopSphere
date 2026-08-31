@extends('admin.layouts.app')

@section('title')
    Coupons
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fa-solid fa-ticket"></i></div>
                    <h3>Coupons <span class="count-pill">{{ $coupons->count() }}</span></h3>
                </div>
                <a href="{{route('admin.coupons.create')}}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
            <div class="card ss-panel">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Discount</th>
                                <th>Validity</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $key => $coupon)
                                <tr>
                                    <td class="mono">{{ $key += 1 }}</td>
                                    <td>{{ $coupon->name }}</td>
                                    <td class="mono">{{ $coupon->discount }}%</td>
                                    <td>
                                        @if ($coupon->checkIfValid())
                                            <span class="badge bg-success">
                                                Valid until {{ \Carbon\Carbon::parse($coupon->valid_until)->diffForHumans()}}
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                Expired
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('admin.coupons.edit',$coupon->id)}}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="deleteItem({{$coupon->id}})" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form id="{{$coupon->id}}" action="{{route('admin.coupons.destroy',$coupon->id)}}" method="post">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($coupons->isEmpty())
                        <div class="ss-empty">No coupons yet — add your first one above.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
