@extends('layouts.app')
@section('content')
<div class="container">
    @can('admin-authors')
    @foreach($authors as $author)
    <div class="row text-center">
        <a href="{{route('admin.authors.edit')"><h1>{{{$author->name}}}</h1>  </a>
        </div>
    @endforeach

    @endcan
</div>
@endsection