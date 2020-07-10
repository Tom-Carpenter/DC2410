@extends('layouts.app')

@section('content')

<div class="container">
  @if(!$filtered and (count($orders)>0))

  <button type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#filterModal">
    Filter results
  </button>
  @endif
  @if($filtered==true and (count($orders)>0))
  <form action="{{route('order.index')}}">
    <button type="submit" class="btn btn-block btn-danger">
      Clear filter
    </button>
  </form>
  @endif
  <!-- modal filter panel -->
  @auth
  @if(count($orders)>0)
  <div class="modal fade " id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModal"
    aria-hidden="true">
    <div class="modal-dialog modal-lg ">
      <div class="modal-content bg-dark">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Filter orders</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('order.index')}}" method="GET">
          <div class="modal-body card card-body bg-dark text-light">
            @csrf
            <input type="hidden" name="filter" value={{true}} />
            @can('view-all-orders')
            <div class="form-group">
              <label for="email">Email address</label>
              <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                autocomplete="email">
              @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>
            @endcan
            <div class="form-group">
              <label for="description">Filter by order total less than (GBP)</label>
              <input name="price_lower" class="form-control" type="number" min="0.00" step="1.00" max="2500" />
            </div>
            <div class="form-group">
              <label for="description">Filter by order total greater than (GBP)</label>
              <input name="price_upper" class="form-control" type="number" min="0.00" step="1.00" max="2500" />
            </div>
            <div class="form-group">
              <label for="date_start">Date starting:</label>
              <input type="datetime-local" id="date_start" name="date_start">
              <label for="birthday">Date ending:</label>
              <input type="datetime-local" id="date_end" name="date_end">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-block btn-primary">Filter</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <table class="table table-dark table-striped">
    <thead class="thead-dark">
      <tr>
        <th scope="col">@sortablelink('id')</th>
        <th>Email</th>
        <th scope="col">Books</th>
        <th scope="col">@sortablelink('total')</th>
        <th scope="col">@sortablelink('updated_at','Date order made')</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orders as $key => $order)
      <tr>
        <th scope="row">{{{$order->id}}}</th>
        <td>{{{$order->user->email}}}</td>
        <td>
          @foreach($order->books as $book)
          <p>{{{$book->name}}} X {{{$book->pivot->quantity}}} = {{{$book->pivot->sub_total}}}</p>
          @endforeach
        </td>
        <td>{{{$order->total}}}</td>
        <td>{{{$order->updated_at}}}
      </tr>
      @endforeach
    </tbody>
  </table>
  {!! $orders->appends(\Request::except('page'))->render() !!}
  @elseif($filtered)
  <div class="card card-body bg-dark">
    <h1 class="text-center">Couldn't find any orders for that criteria</h1>
    <form action="{{route('order.index')}}">
      <button type="submit" class="btn btn-block btn-danger">
        Clear filter
      </button>
    </form>
  </div>
  @else
  <div class='card card-body bg-dark'>
    <h1 class='text-center'> You haven't made any orders yet - we would love for you to start today!</h1>
  </div>
  @endif
  @endauth
</div>
@endsection