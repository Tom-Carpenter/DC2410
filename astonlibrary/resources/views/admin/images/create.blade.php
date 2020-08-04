@extends('layouts.app')
@section('content')

@can('admin-images')
<div class="card card-body bg-dark ">
    <form action="{{route('admin.images.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <div class="form-check">
                <label for="selectedBook">Select a book to upload image for</label>
                <select name="book" class="form-control" id="selectedBook">
                    @foreach($books as $book)
                    <option value="{{$book->id}}">{{{$book->name}}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="fileToUpload">Select an image to upload</label>
            <input class="form-control-file" type="file" name="fileToUpload" id="fileToUpload" />
        </div>
        @if(isset($path))
        <h1> You uploaded </h1>
        <img src="{{$path}}" alt="preview_image" class="d-block w-100">
        @endif
        <button class="btn btn-span btn-primary" type="submit" value="Upload Image" name="submit">Upload</button>
    </form>
</div>
@endcan
@endsection