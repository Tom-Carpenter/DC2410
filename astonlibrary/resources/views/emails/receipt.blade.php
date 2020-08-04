@component('mail::message')
<div>
    <h1>
        Hello,
        {{{' ' .$username}}}</h1>
    <p> Your unique order number is:
        {{{$order->id}}}</p>
    <p>This was placed at: {{{$order->updated_at}}}</p>
    <br />
    @foreach($order->books as $book)
    <p>{{{$book->name}}}<?php echo " X " ?>{{{$book->pivot->quantity}}} =£ {{{$book->pivot->sub_total}}}</p>
    @endforeach
    <br />
    <h1>Order Total: £{{{$order->total}}}</h1>

    Thanks for your order,
    <br>
</div>
{{ config('app.name') }}
@endcomponent