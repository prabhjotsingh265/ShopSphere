@extends('admin.layouts.app')

@section('title')
    Brands
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fa-solid fa-copyright"></i></div>
                    <h3>Brands <span class="count-pill">{{ $brands->count() }}</span></h3>
                </div>
                <a href="{{route('admin.brands.create')}}" class="btn btn-sm btn-primary">
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
                                <th>Slug</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($brands as $key => $brand)
                                <tr>
                                    <td class="mono">{{ $key += 1 }}</td>
                                    <td>{{ $brand->name }}</td>
                                    <td class="mono">{{ $brand->slug }}</td>
                                    <td>
                                        <a href="{{route('admin.brands.edit',$brand->slug)}}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="deleteItem({{$brand->id}})" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form id="{{$brand->id}}" action="{{route('admin.brands.destroy',$brand->slug)}}" method="post">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($brands->isEmpty())
                        <div class="ss-empty">No brands yet — add your first one above.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
