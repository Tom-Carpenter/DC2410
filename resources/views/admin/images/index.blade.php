@extends('layouts.app')
@section('content')
@can('admin-images')
<div class="card card-body bg-dark">
  <div id="carouselExampleControls" class="carousel slide" data-ride="carousel"
    style="height:40vh;max-height:40vh;max-width:15vw;width:15vw;overflow:hidden;">
    <div class="carousel-inner container">
      @foreach($images as $image)
      @if($image==$images[0])
      <div class="carousel-item active">
        <img class="d-block W-100" src="https://astonbookstore.s3.eu-west-2.amazonaws.com/{{$image->name}}" alt="pic">
      </div>
      @else
      <div class="carousel-item">
        <img class="d-block w-100" src="https://astonbookstore.s3.eu-west-2.amazonaws.com/{{$image->name}}" alt="pic">
      </div>
      @endif
      @endforeach

    </div>
  </div>
  @endcan
  @endsection