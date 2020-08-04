@extends('layouts.app')

@section('content')
<div class="container mx-auto">
  @if(!$filtered)
  <button type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#filterModal">
    Filter results
  </button>
  @endif
  @if($filtered==true)
  <form action="{{route('admin.books.index')}}">
    <button type="submit" class="btn btn-block btn-danger">
      Clear filter
    </button>
  </form>
  @endif
  <!-- modal filter panel -->
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
        <form action="{{route('admin.books.index')}}" method="GET">
          <div class="modal-body card card-body bg-dark text-light">
            @csrf
            <input type="hidden" name="filter" value="{{true}}" />
            <div class="form-group">
              <label for="name">Name</label>
              <input id="name" name="name" type="name" class="form-control" />
            </div>
            <div class="form-group">
              <label for="description">Filter by cost less than (GBP)</label>
              <input name="price_lower" class="form-control" type="number" min="0.00" step="1.00" max="2500" />
            </div>
            <div class="form-group">
              <label for="description">Filter by cost greater than (GBP)</label>
              <input name="price_upper" class="form-control" type="number" min="0.00" step="1.00" max="2500" />
            </div>
            <div class="form-group">
              <label for="year_start">Year published between </label>
              <input type="date" id="year_start" name="year_start">
              <label for="year_end">and (if blank assumed as {{{date('Y')}}} )</label>
              <input type="date" id="year_end" name="year_end">
            </div>
            <div class="form-group">
              <label for="author">Select an author:</label>
              <select class="form-control" name="author" id="author">
                <option value="{{null}}">No selection</option>
                @foreach($authors as $key => $author)
                <option value="{{{$author->id}}}">{{{$author->name}}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="category">Select an category:</label>
              <select class="form-control" name="category" id="category">
                <option value="{{null}}">No selection</option>
                @foreach($categories as $key => $category)
                <option value="{{{$category->id}}}">{{{$category->name}}}</option>
                @endforeach
              </select>
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
    <thead>
      <tr>
        <th scope="col">@sortablelink('id') </th>
        <th scope="col"></th> 
        <th scope="col">@sortablelink('name')</th>
        <th scope="col">Description</th>
        <th scope="col">@sortablelink('price')</th>
        <th scope="col">Categories</th>
        <th scope="col">@sortablelink('stock')</th>
        @can('edit-books')
        @can('delete-books')
        <th scope="col"> Actions </th>
        @endcan
        @endcan

      </tr>
    </thead>
    <tbody>
      @foreach($books as $key => $book)
      <tr>
        <th scope="row">{{{$book->id}}}</th>
        <?php 
    if($book->images() != null){
        $images = $book->images()->get()->pluck('name');
    }
    ?>
     <td>
      @if(!empty($images))
        <img  style="float: left;
    width:  12vw;
    height: 40vh;
    object-fit: cover;"
          src="https://astonbookstore.s3.eu-west-2.amazonaws.com/{{$images[0]}}" alt="pic">
      @endif

  </td>
        <td><a class="text-info" href="{{route('admin.books.show',$book->id)}}">{{{$book->name}}}</a></td>
        <td>{{{$book->description}}}</td>
        <td>{{{$book->price}}}</td>
        <td>
          {{{ implode(', ' ,$book ->categories()->get()->pluck('name')->toArray())}}}
        </td>
        <td>{{{$book->stock}}}</td>
        @can('edit-books')
        @can('delete-books')
        <td>
          <a class="text-light" href="{{route('admin.books.edit', $book)}}"><button type="button"
              class="btn btn-block btn-primary float-left ">Edit</button></a>
</div>
<!--
            <form action="{{route('admin.books.destroy', $book)}}" method="POST" class="float-left" name="delete_book">
                @csrf
                {{ method_field('DELETE') }}
                <button type="submit" class="btn btn-block btn-danger">Delete</button> -->
<!-- dont think i want anyone to delete a book -->
</form>
</td>

@endcan
@endcan
</tr>
</tbody>
@endforeach
</table>
{!! $books->appends(request()->query())->links()!!}
</div>
@endsection
