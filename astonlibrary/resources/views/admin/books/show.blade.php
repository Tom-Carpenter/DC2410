@extends('layouts.app')
@section('content')
<div class="container card bg-dark text-white">
  <?php 
    if($book->images() != null){
        $images = $book->images()->get()->pluck('name');
    }
    ?>
  <div id="carouselExampleControls" class="carousel slide center row col-4 d-flex justify-content-center" data-ride="carousel"
    style="height:10vh;overflow:hidden;">
    <div class="carousel-inner container">
      @if(!empty($images))
      @foreach($images as $image)
      @if($image==$images[0])
      <div class="carousel-item active">
        <img style="padding-top:3%;padding-left" class="d-block w-100 img-fluid"
          src="https://astonbookstore.s3.eu-west-2.amazonaws.com/{{$image}}" alt="pic">
      </div>
      @else
      <div class="carousel-item">
        <img class="d-block w-100 img-fluid" src="https://astonbookstore.s3.eu-west-2.amazonaws.com/{{$image}}" alt="pic">
      </div>
      @endif
      @endforeach
      @endif
    </div>
  </div>
  <h1 class="card-header">{{{$book->name}}}</h1>
  <div class="card-body">
    <p>Published in year {{{$book->published_year}}}</p>
    <h1 class="card-title">{{{ implode(', ' ,$book ->authors()->get()->pluck('name')->toArray()) }}}</h1>
    <h5 class="card-title">{{{ implode(', ' ,$book ->categories()->get()->pluck('name')->toArray()) }}}</h5>
    <div class="row">
      <h4 class="card-text col-xs-12 col-sm-6">£{{{$book->price}}}</h4>
      <h5 class="card-text col-xs-12 col-sm-6 ">There is {{{$book->stock}}} left in stock</h5>
    </div>
    <div class="row">
      <p class="card-text">{{{$book->description}}}</p>
    </div>
    <div class="card-footer">
      @auth
      <form method="POST" action="{{ route('order.add',$book->id) }}">
        @csrf
        <label for="stock">Quantity</label>
        <div class="input-group mb-3">
          <input required name="quantity" pattern="^[0-9]*$" class="form-control" type="number" min="0" step="1"
            max="1000" value="1" />
          <div class="input-group-append">
            <button type="submit" class="btn btn-success">Add to basket</button>
          </div>
        </div>

      </form>
      @endauth
      <a href="{{ url()->previous() }}" class="btn btn-primary btn-block">Back</a>
    </div>
  </div>
</div>
</div>
@endsection