@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card bg-dark text-white">
            <div class="card-body">
                @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif
                @guest
                <div class="container text-center">
                    <!-- Welcome header for guest  -->


                    <!-- Login    Register -->
                    <div class="row">
                        @if (Route::has('register'))
                        <div class="col-md-12" style="padding-bottom:5%;">
                            <a class="nav-link" href="{{ route('register') }}">
                                <button type="button"
                                    class="btn btn-lg btn-primary btn-block float-left ">{{ __('Register') }}</button>
                            </a>
                        </div>
                        @endif
                        <div class="col-md-12" style="padding-bottom:5%;">
                            <a class="nav-link" href="{{ route('login') }}">
                                <button type="button"
                                    class="btn btn-lg btn-primary btn-block float-left ">{{ __('Login') }}</button>
                            </a>
                        </div>
                    </div>
                    <!-- Continue as guest -->
                    <a class="nav-link" href="{{route('admin.books.index')}}">
                        <button type="button" class="btn btn-lg btn-success btn-block float-left ">Continue as
                            guest</button>
                    </a>

                </div>
                @else
                <div class="container text-center">
                    <div class="row">
                        <div class="col-sm-8 offset-sm-2">
                            @if(Auth::user())
                            <h1>Welcome {{ Auth::user()->name}}</h1>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-8 offset-sm-2">
                            <a href="{{route('admin.books.index')}}">
                                <h3>To the Aston book store</h3>
                            </a>
                        </div>
                    </div>
                    <div class="row col">
                        <img style="padding-left:3%;"
                            src="https://astonbookstore.s3.eu-west-2.amazonaws.com/resources/bookstore_clipart.jpg" />
                    </div>
                </div>
                @endguest

            </div>
        </div>
    </div>
</div>
@endsection