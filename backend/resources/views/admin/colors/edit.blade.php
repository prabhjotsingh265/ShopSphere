@extends('admin.layouts.app')

@section('title')
    Edit color
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fa-solid fa-palette"></i></div>
                    <h3>Edit Color</h3>
                </div>
                <a href="{{route('admin.colors.index')}}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            <div class="ss-form-card ss-reveal">
                <form action="{{route('admin.colors.update',$color->id)}}" method="post">
                    @csrf
                    @method("PUT")
                    <div class="mb-3">
                        <label for="name" class="form-label">Name*</label>
                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            name="name"
                            id="name"
                            value="{{old('name',$color->name)}}"
                            aria-describedby="helpId"
                            placeholder="Name*"
                        />
                        @error('name')
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
