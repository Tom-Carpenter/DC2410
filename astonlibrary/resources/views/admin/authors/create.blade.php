@extends('layouts.app')
@section('content')
<div class="container">
    @can('admin-authors')
    <div class="card card-body bg-dark text-center">
        <form action="{{route('admin.authors.store')}}" method="POST">
            @csrf
            <label for="name">Enter the authors name: </label>
            <input id="name" type="text" class="form-control" name="name" required>
            @error('name')
            <div class="error text-danger">{{ $message }}</div>
            @enderror

            <br />
            <button type="submit" class="btn btn-primary btn-block"> Add author </button>
        </form>
        <h1> Existing authors </h1>
        <div id="list-authors" class="list-group" style="max-height:50vh;overflow:scroll;">
            @foreach($authors as $author)
            <a class="list-group-item list-group-item-action text-dark">
                <h5>{{{$author->name}}}</h5>
            </a>
            @endforeach
        </div>
        @endcan
    </div>
    @endsection