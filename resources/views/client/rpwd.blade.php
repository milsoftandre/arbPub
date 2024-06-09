
@extends('auth.layout')
@section('pageTitle', 'Recover password')
@section('content')
    <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url(assets/media/illustrations/sketchy-1/14.png">
        <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
            <a href="#" class="mb-12">
                <img alt="Logo" src="img/main-notebook.png" class="h-40px" />
            </a>

            <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">


                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <h3 class="card-header text-center">Recover password</h3>
                            <div class="card-body">
                                @if ($message = Session::get('success'))
                                    <div class="alert alert-success">
                                        {{ $message }}
                                    </div>
                                @endif
                                <form method="POST" action="{{ route('rstore') }}">
                                    @csrf

                                    <div class="form-group mb-3">
                                        <input type="text" placeholder="Login (email)" id="email" class="form-control" name="email" required
                                               autofocus>
                                        @if ($errors->has('email'))
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>

                                    <div class="d-grid mx-auto">
                                        <button type="submit" class="btn btn-dark btn-block">Recover password</button>
                                    </div>
                                    <br><br>
                                    <a href="{{ route('login') }}">Sing in</a>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>
@endsection