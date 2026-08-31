@extends('admin.layouts.app')

@section('title')
    Categories
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fa-solid fa-layer-group"></i></div>
                    <h3>Categories <span class="count-pill">{{ $categories->count() }}</span></h3>
                </div>
                <a href="{{route('admin.categories.create')}}" class="btn btn-sm btn-primary">
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
                            @foreach ($categories as $key => $category)
                                <tr>
                                    <td class="mono">{{ $key += 1 }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td class="mono">{{ $category->slug }}</td>
                                    <td>
                                        <a href="{{route('admin.categories.edit',$category->slug)}}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="deleteItem({{$category->id}})" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form id="{{$category->id}}" action="{{route('admin.categories.destroy',$category->slug)}}" method="post">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($categories->isEmpty())
                        <div class="ss-empty">No categories yet — add your first one above.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
