@extends('admin.layouts.app')

@section('title')
    Products
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fa-solid fa-tag"></i></div>
                    <h3>Products <span class="count-pill">{{ $products->count() }}</span></h3>
                </div>
                <a href="{{route('admin.products.create')}}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
            <div class="card ss-panel">
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <td>#</td>
                                <td>Name</td>
                                <td>Slug</td>
                                <td>Category</td>
                                <td>Brand</td>
                                <td>Colors</td>
                                <td>Sizes</td>
                                <td>Qty</td>
                                <td>Price</td>
                                <td>Images</td>
                                <td>Status</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $key => $product)
                                <tr>
                                    <td>{{ $key += 1 }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->slug }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->brand->name }}</td>
                                    <td>
                                        @foreach ($product->colors as $color)
                                            <span class="badge bg-light text-dark">
                                                {{ $color->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($product->sizes as $size)
                                            <span class="badge bg-light text-dark">
                                                {{ $size->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>{{ $product->qty }}</td>
                                    <td class="mono">${{ number_format($product->price, 2) }}</td>
                                    <td>
                                        <img src="{{asset($product->thumbnail)}}"
                                            alt="{{ $product->name }}"
                                            class="img-fluid rounded mb-1"
                                            width="30"
                                            height="30"
                                        >
                                        @if ($product->first_image)
                                            <img src="{{asset($product->first_image)}}"
                                                alt="{{ $product->name }}"
                                                class="img-fluid rounded mb-1"
                                                width="30"
                                                height="30"
                                            >
                                        @endif
                                        @if ($product->second_image)
                                            <img src="{{asset($product->second_image)}}"
                                                alt="{{ $product->name }}"
                                                class="img-fluid rounded mb-1"
                                                width="30"
                                                height="30"
                                            >
                                        @endif
                                        @if ($product->third_image)
                                            <img src="{{asset($product->third_image)}}"
                                                alt="{{ $product->name }}"
                                                class="img-fluid rounded mb-1"
                                                width="30"
                                                height="30"
                                            >
                                        @endif
                                    </td>
                                    <td>
                                        @if ($product->status)
                                            <span class="badge bg-success p-2">
                                                In Stock
                                            </span>
                                        @else
                                            <span class="badge bg-danger p-2">
                                                Out of Stock
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('admin.products.edit',$product->slug)}}" class="btn btn-sm btn-warning mb-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="deleteItem({{$product->id}})" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form id="{{$product->id}}" action="{{route('admin.products.destroy',$product->slug)}}" method="post">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    @if ($products->isEmpty())
                        <div class="ss-empty">No products yet — add your first one above.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
