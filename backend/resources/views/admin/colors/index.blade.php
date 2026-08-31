@extends('admin.layouts.app')

@section('title')
    Colors
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fa-solid fa-palette"></i></div>
                    <h3>Colors <span class="count-pill">{{ $colors->count() }}</span></h3>
                </div>
                <a href="{{route('admin.colors.create')}}" class="btn btn-sm btn-primary">
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
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($colors as $key => $color)
                                <tr>
                                    <td class="mono">{{ $key += 1 }}</td>
                                    <td>{{ $color->name }}</td>
                                    <td>
                                        <a href="{{route('admin.colors.edit',$color->id)}}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="deleteItem({{$color->id}})" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form id="{{$color->id}}" action="{{route('admin.colors.destroy',$color->id)}}" method="post">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($colors->isEmpty())
                        <div class="ss-empty">No colors yet — add your first one above.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
