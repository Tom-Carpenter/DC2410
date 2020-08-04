@extends('layouts.app')
@section('content')
@auth
@if($order)
<div class="container text-center card bg-dark">
    <div class="row">
        <div class="card-body ">
            <h3 class="card-title">Order summary:</h3>
            <h5 class="card-title">{{{$order->user->email}}}</h5>
            <div class="card-text">
                @foreach($order->books as $book)
                <p>
                    <h4>
                        {{{$book->name}}} X
                        {{{$book->pivot->quantity}}}
                    </h4>
                </p>
                @endforeach
            </div>
        </div>
    </div>
    <div class="card-body">
        <h1>Total : £{{$order->total}}</h1>
    </div>
    <div class="card-body ">
        <form action="{{route('order.store',$order)}}" method="POST">
            @csrf
            <label for="email">Enter the email address you want the receipt sending too</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                value="{{ $order->user->email }}" autocomplete="email">
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            </br>
            <a href="{{route('admin.books.index')}}" type="button" class="btn btn-block btn-secondary">Continue
                Shopping</a>
            <button type="submit" class="btn btn-block btn-primary">Complete purchase</button>
        </form>
    </div>
</div>
@else
<div class="container text-center card bg-dark">
    <h1> Nothing in your basket! </h1>
</div>
@endif
@endauth
@endsection