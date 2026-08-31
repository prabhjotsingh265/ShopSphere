@extends('admin.layouts.app')

@section('title')
    My Profile
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fas fa-user-gear"></i></div>
                    <h3>My Profile</h3>
                </div>
            </div>
            <div class="ss-form-card ss-reveal">
                <form action="{{route('admin.profile.update')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="{{$admin->image_path}}" alt="{{$admin->name}}"
                            id="profile_image_preview"
                            width="72" height="72"
                            class="rounded-circle"
                            style="object-fit: cover; border: 2px solid var(--line);"
                        >
                        <div class="flex-grow-1">
                            <label for="profile_image" class="form-label">Profile Photo</label>
                            <input
                                type="file"
                                class="form-control @error('profile_image') is-invalid @enderror"
                                name="profile_image"
                                id="profile_image"
                            />
                            @error('profile_image')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Name*</label>
                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            name="name"
                            id="name"
                            value="{{old('name',$admin->name)}}"
                        />
                        @error('name')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email*</label>
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email"
                            id="email"
                            value="{{old('email',$admin->email)}}"
                        />
                        @error('email')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <hr class="my-4">
                    <p class="text-muted small mb-3">Leave the password fields blank to keep your current password.</p>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            id="password"
                        />
                        @error('password')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input
                            type="password"
                            class="form-control"
                            name="password_confirmation"
                            id="password_confirmation"
                        />
                    </div>

                    <button type="submit" class="btn btn-sm btn-dark">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
