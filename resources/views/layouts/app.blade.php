<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Aston book store</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body style="background-color:RGB(111, 115, 128); color:white;">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    Aston book store
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="{{ route('admin.books.index') }}"> Take me to the library... <span
                                    class="sr-only">(current)</span></a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @auth
                        <li style="padding-right:2vw"class="nav-item bg-dark text-white">
                            <a  class ="bg-gray text-white" href="{{route('order.showBasket')}}">
                                Shopping basket<i class="nav-link material-icons">add_shopping_cart</i>
                            </a></li >
                        @endauth
                        @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @endif
                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right bg-dark text-white" aria-labelledby="navbarDropdown">
                               
                                <a class="dropdown-item bg-dark text-white" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                @can('create-books')
                                <a class="dropdown-item bg-dark text-white" href="{{ route('admin.books.create') }}">
                                    Add book
                                </a>
                                @endcan
                                @can('manage-users')
                                <a class="dropdown-item bg-dark text-white" href="{{ route('admin.users.index') }}">
                                    Manage Users
                                </a>
                                @endcan
                                @can('admin-authors')
                                <a class="dropdown-item bg-dark text-white" href="{{ route('admin.authors.create') }}">
                                    Manage Authors
                                </a>
                                @endcan
                                @can('admin-images')
                                <a class="dropdown-item bg-dark text-white" href="{{ route('admin.images.create') }}">
                                    Add images
                                </a>
                                @endcan
                                @if(Gate::check('view-all-orders'))
                                <a class="dropdown-item bg-dark text-white" href="{{ route('order.index') }}">
                                    View all orders
                                </a>
                                @elseif(Auth::user())
                                <a class="dropdown-item bg-dark text-white" href="{{ route('order.index') }}">
                                    View your previous orders
                                </a>
                                @endif
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Modal -->
        <main class="py-4">
            <div class="container">
                @include('partials.alerts')
                @yield('content')
            </div>
        </main>
    </div>
    <div class="modal fade" id="shoppingBasketModal" tabindex="-1" role="dialog" aria-labelledby="shoppingBasketModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Shopping basket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if(Session::get('order')!=null)
                    <?php $basket=Session::get('order'); ?>
                    @foreach($basket as $bookname => $quantity)
                    <h3>{{$bookname}}</h3>
                    <h5><?php echo "  " ?> X {{$quantity}}</h5>
                    @endforeach
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
                </p>
            </div>
        </div>

</body>

</html>