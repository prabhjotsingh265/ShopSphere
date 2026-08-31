@extends('admin.layouts.app')

@section('title')
    Edit coupon
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fa-solid fa-ticket"></i></div>
                    <h3>Edit Coupon</h3>
                </div>
                <a href="{{route('admin.coupons.index')}}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            <div class="ss-form-card ss-reveal">
                <form action="{{route('admin.coupons.update',$coupon->id)}}" method="post">
                    @csrf
                    @method("PUT")
                    <div class="mb-3">
                        <label for="name" class="form-label">Name*</label>
                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            name="name"
                            id="name"
                            value="{{old('name',$coupon->name)}}"
                            aria-describedby="helpId"
                            placeholder="Name*"
                        />
                        @error('name')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="discount" class="form-label">Discount*</label>
                        <input
                            type="number"
                            class="form-control @error('discount') is-invalid @enderror"
                            name="discount"
                            id="discount"
                            value="{{old('discount',$coupon->discount)}}"
                            aria-describedby="helpId"
                            placeholder="Discount*"
                        />
                        @error('discount')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="valid_until" class="form-label">Validity*</label>
                        <input
                            type="date"
                            min="{{\Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}"
                            value="{{old('valid_until', $coupon->valid_until->format('Y-m-d'))}}"
                            class="form-control @error('valid_until') is-invalid @enderror"
                            name="valid_until"
                            id="valid_until"
                            aria-describedby="helpId"
                            placeholder="Validity*"
                        />
                        @error('valid_until')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <button
                        type="submit"
                        class="btn btn-sm btn-dark"
                    >
                        Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
