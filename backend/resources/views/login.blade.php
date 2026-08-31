<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ShopSphere Admin Login</title>
        <link rel="icon" type="image/svg+xml" href="{{asset('favicon.svg')}}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@700;900&family=Schibsted+Grotesk:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap">
        <link href="{{asset('css/theme.css')}}" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <div class="row my-5">
                <div class="col-md-6 mx-auto">
                    <div class="text-center mb-4">
                        @include('admin.layouts.logo', ['size' => 44])
                    </div>
                    @session('error')
                        <div class="alert alert-danger my-2">
                            {{ session('error') }}
                        </div>
                    @endsession
                    <div class="card shadow-sm p-5">
                        <div class="card-header bg-white text-center">
                            <h3 class="mt-2">
                                Login
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="{{route("admin.auth")}}" method="post">
                                @csrf
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="floatingInput" 
                                        name="email" placeholder="name@example.com">
                                    <label for="floatingInput">Email address*</label>
                                    @error('email')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        name="password" id="floatingPassword" placeholder="Password">
                                    <label for="floatingPassword">Password*</label>
                                    @error('password')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-2">
                                    <button type="submit" class="btn btn-lg btn-dark">
                                        Sign in
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>