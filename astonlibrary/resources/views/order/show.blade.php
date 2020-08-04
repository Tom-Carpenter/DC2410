@extends('layouts.app')
@section('content')
@auth
<div class="container">
    <div class="card bg-dark text-white">
        <h5 class="card-header">{{{Auth::user()->name}}}</h5>
        @if(!$order->books->count())
        <div class="card-body text-center">
            <h1>Basket is empty</h1>
            <span class="material-icons">
                shopping_basket
            </span>
        </div>
        @else
        <div class="card-body">
            <h5 class="card-title">Order details {{{$order->id}}}</h5>
            <div class="row">
                <div class="col">
                    Title
                </div>
                <div class="col">
                    Price
                </div>
                <div class="col">
                    Quantity
                </div>
                <div class="col">
                    Sub Total
                </div>
                <div class="col">

                </div>
            </div>
            @foreach($order->books as $book)
            <form action="{{route('order.update',$order)}}" method="POST">
                @csrf
                {{ method_field('PUT') }}
                <!-- <form  action="{{route('order.update',$order)}}" method="POST"> -->
                <div class="row">
                    <div class="col">
                        {{{$book->name}}}
                    </div>
                    <div class="col">
                        Â£{{{$book->price}}}
                    </div>

                    <div class="col">
                        <input type="hidden" name="bookid" value="{{{$book->id}}}" />
                        <input required name="quantity" pattern="^[0-9]*$" class="form-control" type="number" min="1"
                            step="1" max="{{{$book->stock}}}" value="{{{$book->pivot->quantity}}}" />
                    </div>
                    <div class="col">
                        {{{$book->pivot->sub_total}}}
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
            </form>
            <form action="{{route('order.remove', $order->id)}}" method="POST" class="float-left" name="delete_book">
                @csrf
                {{ method_field('POST') }}
                <input type="hidden" name="bookid" value="{{$book->id}}" />
                <input type="hidden" name="quantity" value="{{$book->pivot->quantity}}" />
                <input type="hidden" name="sub_total" value="{{$book->pivot->sub_total}}" />
                <button type="submit" class="btn btn-danger">Remove</button>
            </form>
        </div>
    </div>
    <!--</form> -->
    <br/>
    @endforeach
    <h1> Total : {{ utf8_encode("£") }} {{{$order->total}}}</h1>
    <a type="button" href="{{route('order.create')}}" class="btn btn-primary">Proceed to checkout</a>

</div>
</div>
@endif
<br/>
<a type="button" href="{{route('order.index')}}" class="btn btn-primary btn-block">View previous orders</a>

</div>
@endauth
@endsection