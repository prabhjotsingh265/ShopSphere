@extends('admin.layouts.app')

@section('title')
    Sizes
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fa-solid fa-expand"></i></div>
                    <h3>Sizes <span class="count-pill">{{ $sizes->count() }}</span></h3>
                </div>
                <a href="{{route('admin.sizes.create')}}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
            <div class="card ss-panel">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <td>#</td>
                                <td>Name</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sizes as $key => $size)
                                <tr>
                                    <td>{{ $key += 1 }}</td>
                                    <td>{{ $size->name }}</td>
                                    <td>
                                        <a href="{{route('admin.sizes.edit',$size->id)}}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="deleteItem({{$size->id}})" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form id="{{$size->id}}" action="{{route('admin.sizes.destroy',$size->id)}}" method="post">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($sizes->isEmpty())
                        <div class="ss-empty">No sizes yet — add your first one above.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
