@extends('admin.layouts.app')

@section('title')
    Add new category
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fa-solid fa-layer-group"></i></div>
                    <h3>Add New Category</h3>
                </div>
                <a href="{{route('admin.categories.index')}}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            <div class="ss-form-card ss-reveal">
                <form action="{{route('admin.categories.store')}}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name*</label>
                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            name="name"
                            id="name"
                            value="{{old('name')}}"
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
