@extends('layouts.app')
@section('content')
@can('create-books')
<div class="card bg-dark card-body">
  <h1 class="card-title">Add a new book</h1>
  <div class="modal fade" id="authorModal" tabindex="-1" role="dialog" aria-labelledby="authorModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content bg-dark">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">New author</h5>
          <button type="button btn btn-danger" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
            @can('admin-authors')
            <div class="card card-body bg-dark text-center">
              <form action="{{route('admin.authors.store')}}" method="POST">
                @csrf
                <input type="hidden" value="{{true}}" name="book" />
                <label for="name">Enter the authors name: </label>
                <input id="name" type="text" class="form-control" name="name" required>
                <br />
                <button type="submit" class="btn btn-primary btn-block"> Add author </button>
              </form>
              @endcan
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-block" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <form action="{{route('admin.books.store') }}" method="POST">
    @csrf
    <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#authorModal">
      Add a new author
    </button>
    <div class="form-group">
      <label for="name">Name:</label>
      <input required name="name" type="name" class="form-control" id="bookname">
      @error('name')
      <div class="error text-danger">{{ $message }}</div>
      @enderror

    </div>
    <div class="form-group">
      <label for="description">Description:</label>
      <textarea required name="description" class="form-control" id="description"></textarea>
      @error('description')
      <div class="error text-danger">{{ $message }}</div>
      @enderror

    </div>
    <div class="form-group">
      <label for="description">Price: (GBP)</label>
      <input required name="price" class="form-control" type="number" min="0.00" step="0.10" max="2500" value="0.0" />
      @error('price')
      <div class="error text-danger">{{ $message }}</div>
      @enderror
    </div>
    <div class="form-group">
      <label for="yearPublished">Year published:</label>
      <input required name="yearPublished" pattern="^[0-9]*$" type="number" min="0000" max="2300" step="1"
        value="2020" />
      @error('yearPublished')
      <div class="error text-danger">{{ $message }}</div>
      @enderror

    </div>
    <div class="form-group">
      <label for="description">Stock:</label>
      <input required name="stock" pattern="^[0-9]*$" class="form-control" type="number" min="0" step="1" max="1000"
        value="0" />
      @error('stock')
      <div class="error text-danger">{{ $message }}</div>
      @enderror

    </div>
    <div class="form-group">
      <label>Select any existing authors (hold ctrl to select multiple)</label>
      <select multiple="multiple" name="authors[]" class="custom-select" size="10">
        @foreach($authors as $author)
        <option type="checkbox" value="{{$author->id}}">{{$author->name}}</option>
        @endforeach
      </select>
      <div class="form-group">
        <label>Select the relevant categories</label>
        <div class="form-group">
          @foreach($categories as $category)
          <div class="form-check">
            <input type="checkbox" name="categories[]" class="form-check-input" value="{{$category->id}}">
            <label class="form-check-label" for="{{$category->id}}">{{$category->name}}</label>
          </div>
          @endforeach
        </div>
      </div>
      <button type="submit" class="btn btn-block btn-primary">Add new book</button>
      <a href="javascript:history.back()" class="btn btn-block btn-danger">Cancel</a>
  </form>
</div>
@endsection
@endcan