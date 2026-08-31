@extends('admin.layouts.app')

@section('title')
    Users
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fa-solid fa-users"></i></div>
                    <h3>Users <span class="count-pill">{{ $users->count() }}</span></h3>
                </div>
            </div>
            <div class="card ss-panel">
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Profile Image</th>
                                <th>Registred</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $key => $user)
                                <tr>
                                    <td class="mono">{{ $key += 1 }}</td>
                                    <td>
                                        {{ $user->name }}
                                    </td>
                                    <td>
                                        {{ $user->email }}
                                    </td>
                                    <td>
                                        <img src="{{$user->image_path}}"
                                            alt="{{ $user->name }}"
                                            class="rounded"
                                            width="60"
                                            height="60"
                                        >
                                    </td>
                                    <td class="mono">{{ $user->created_at->diffForHumans() }}</td>
                                    <td class="d-flex">
                                        <a href="#" onclick="deleteItem({{$user->id}})" class="btn btn-sm btn-danger mx-1">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form id="{{$user->id}}" action="{{route('admin.users.delete',$user->id)}}" method="post">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    @if ($users->isEmpty())
                        <div class="ss-empty">No customer accounts yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
