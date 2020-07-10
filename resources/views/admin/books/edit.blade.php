@extends('layouts.app')

@section('content')

<div class="container">
<div class="card card-body bg-dark text-white row">
<div class="modal fade" id="authorModal" tabindex="-1" role="dialog" aria-labelledby="authorModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New author</h5>
        <button type="button" class="close btn btn-danger" data-dismiss="modal" aria-label="Close">
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
        </input>
        </br>
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

        <form action="{{ route('admin.books.update',$book) }}" method="POST">
        @csrf
        {{ method_field('PUT') }}
        
        <div class="form-group">
            <label for="name">Name:</label>
            <input required name="name" type="name" class="form-control" id="bookname" value="{{{$book->name}}}">
		@error('name')
                <div class="error text-danger">{{ $message }}</div>
                @enderror

        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea required name="description" class="form-control" id="description">{{{$book->description}}}</textarea>
@error('description')
                <div class="error text-danger">{{ $message }}</div>
                @enderror

        </div>
        <div class="form-group">
            <label for="price">Price: (GBP)</label>
            <input required name="price" class="form-control" type="number" min="0.00" step="0.10" max="2500" value="{{{$book->price}}}" />
        </div>
@error('price')
                <div class="error text-danger">{{ $message }}</div>
                @enderror

        <div class="form-group">
            <label for="stock">Stock:</label>
            <input required name="stock" pattern="^[0-9]*$" class="form-control" type="number" min="0" step="1" max="1000" value="{{{$book->stock}}}" />
@error('stock')
                <div class="error text-danger">{{ $message }}</div>
                @enderror
        </div>
	 <div class="form-group">
            <label for="yearPublished">Year published:</label>
            <input required name="yearPublished" pattern="^[0-9]*$" type="number" min="1900" max="2300" step="1" value="{{$book->published_year}}" />
		@error('yearPublished')
                <div class="error text-danger">{{ $message }}</div>
                @enderror

        </div>
	<button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#authorModal">
  Add a new author
</button>

    
        <div class="form-group">
        <label>Select any existing authors</label>
    <select multiple="multiple" name="authors[]" class="custom-select" size="10">
        @foreach($authors as $author)
            <option 
            type="checkbox" 
            value="{{$author->id}}"
            @if($book->authors->pluck('id')->contains($author->id)) selected @endif
            >
                {{{$author->name}}}
                </option>
        @endforeach
    </select>
        </div>
        <div class="form-group">
                @foreach($categories as $category)
                <div class="form-check">
                    <input type="checkbox" name="categories[]" class="form-check-input" value="{{$category->id}}"
                    @if($book->categories->pluck('id')->contains($category->id)) checked @endif
                    >
                        <label class="form-check-label" for="{{$category->id}}">{{{$category->name}}}</label>
                </div>
                @endforeach
        </div>
        <a href=" {{ route('admin.books.index') }}">
            <button type="button" class="btn btn-block btn-danger">Cancel</button>
        </a>
        <div style="padding:1vh;"></div>
        <button type="submit" class="btn btn-block btn-primary">Save</button>
        </form>
    </div>
    
</div>
@endsection