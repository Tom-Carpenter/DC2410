<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Book;
use App\Author;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Gate;

class BookController extends Controller
{
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request,Book $books)
    {   
        /**
        * A method to handle the filtering of App\Book by name, price_lower(i.e less than this price), price_upper (i.e more than this price)
        * year of publish - start date and end date (i.e range) where if end date not provided then end date assumed to be current date.
        * App\Authors and App\Categories 
        */
        function filter(Request $request){
        
            /**
            *	To handle the escape characters for SQL like querying 
            *       @author ntzm. @https://stackoverflow.com/questions/22749182/laravel-escape-like-clause/42028380#42028380  
            *	@returns String 
            */
            function escape_like(string $value, string $char = '\\'): string
            {
                return str_replace(
                    [$char, '%', '_'],
                    [$char.$char, $char.'%', $char.'_'],
                    $value
                );
            }   

            $books =Book::sortable();
        //if no end year provided then set to current timestamp
            if(!isset($request->year_end)){
                $year_end = date('Y');//use current time stamp
            }else{
                $year_end = $request->year_end; //use the provided date time
            }
        //build the query based on the specific scenarios of provided filter criteria 
            $books->when($request->name,function($query,$name){
                return $query->where('name','LIKE','%' . escape_like($name) . '%');
            })->when($request->price_lower,function($query,$cost){
                return $query->where('price','<=',$cost);
            })->when($request->price_upper,function($query,$cost){
                return $query->where('price','>',$cost);
            });
        //handling the more complicated filter types
            if($request->year_start){
                $books->whereBetween('published_year',[$request->year_start,$year_end]);
            }elseif($request->year_end){
                $books->where('published_year','<=',$year_end);
            }
            if(isset($request->author)){
                $authorid = $request->author;
                $books->whereHas('authors',function($query) use($authorid){
                    $query->where('authors.id',$authorid);
                });
            }
            if(isset($request->category)){
                $categoryid = $request->category;
                $books->whereHas('categories',function($query) use($categoryid){
                    $query->where('categories.id',$categoryid);
                });
            }
            //return results
            $books=$books->paginate(4);
            return $books; 
        }
	//if required to filter then call filter
        if(isset($request->filter)){
            $filtered=true;
            $books = filter($request);
        }else{ // get all books (i.e not filtered)
            $filtered=false;
            $books = Book::sortable()->paginate(4);
        }
        //anyone can view - conditional rendering based on Gated access levels done by view
        $request->session()->flash('info','Click the books name to see more details or add to basket and did you know
         you can also sorter the filter by clicking the column headings!');    
         $authors = Author::all();//this is to allow the edit book modal to have full list of authors (same for categories)   
         return view('admin.books.index')->with(['books'=>$books,'authors'=>$authors,'categories'=>Category::all(),'filtered'=>$filtered]);
    }
    

    /*removed on @15-06-2020
     public function admin()
    {
        $books = Book::all();
        $authors = Author::all();
        return view('/admin/books.index')->with(['books'=>$books,'authors'=>$authors]);
    }
    public function editImage(){
        $books = Book::all();
        return view('/admin/books.add-image')->with('books',$books);
    } */


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Gate::denies('create-books')){
            return redirect(route('admin.books.index'));
        }
        $authors = Author::all();
        $categories = Category::all();
        return view('/admin/books/create')->with(['authors'=>$authors,'categories'=>$categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {	
	
        //validate request params - although front end should prevent any of these reaching here
        //but as failsafe this 
        if(Gate::denies('create-books')){
            return redirect(route('admin.books.index'));
        }
	//validate inbound paramters 
		
	$this->validate($request, [
            'price' => 'required|numeric|gte:0',
            'stock' => 'required|integer|gte:0',
            'name'=>'required|max:255',
            'yearPublished' => 'required|digits:4|integer|min:0000|',
	     'description'=>'required'
        ]);
	if(isset($request->categories)){ // this is to prevent the sync message from silently failing (if a user manually changes the id of role at the front end)
		foreach($request->categories as $category){
			if(!Category::where('id','=',$category)->first()){
				$request->session()->flash('error','The category you tried to assign is not an approved category.');
				return redirect()->route('admin.books.index');
			}
		}
	}
	if(isset($request->authors)){ // this is to prevent the sync message from silently failing (if a user manually changes the id of role at the front end)
		foreach($request->authors as $author){
			if(!Author::where('id','=',$author)->first()){
				$request->session()->flash('error','The author you tried to assign is not a known author, please create the author first using the button below.');
				return redirect()->route('admin.books.index');
			}
		}
	}


        $book = Book::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'published_year' => $request->yearPublished
        ]);

        if(!$book->authors()->sync($request->authors)){
            $request->session()->flash('error','Failed to add the authors to the book.');
            return redirect()->route('admin.books.create');
        }
        if(!$book->categories()->sync($request->categories)){
            $request->session()->flash('error','Failed to add the categories to the book.');
            return redirect()->route('admin.books.create');
        }
        //else - it all worked
        $request->session()->flash('success','You created a new book '. $book->name . ' which has an id of ' . $book->id);
        return redirect()->route('admin.books.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {     
        return view('admin.books.show')->with('book',$book);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        if(Gate::denies('edit-books')){
            return redirect(route('admin.books.index'));
        }
        $authors = Author::all();
        $categories = Category::all();
        return view('admin.books.edit')->with(['book'=>$book,'authors'=>$authors,'categories'=>$categories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
	
        if(Gate::denies('edit-books')){
            return redirect(route('admin.books.index'));
        }
	    $this->validate($request, [
            'price' => 'required|numeric|gte:0',
            'stock' => 'required|integer|gte:0',
            'name'=>'required|max:255',
            'yearPublished' => 'required|digits:4|integer|min:1900|',
	     'description'=>'required'
        ]);
	
         if(isset($request->categories)){ // this is to prevent the sync message from silently failing (if a user manually changes the id of role at the front end)
	
		foreach($request->categories as $category){
			if(!Category::where('id','=',$category)->first()){
				$request->session()->flash('error','The category you tried to assign is not an approved category.');
				return redirect()->route('admin.books.index');
			}
		}
	}
	if(isset($request->authors)){ // this is to prevent the sync message from silently failing (if a user manually changes the id of role at the front end)
		foreach($request->authors as $author){
			if(!Author::where('id','=',$author)->first()){
				$request->session()->flash('error','The author you tried to assign is not a known author, please create the author first using the button below.');
				return redirect()->route('admin.books.index');
			}
		}
	}
        if(!$book->authors()->sync($request->authors)){
            $request->session()->flash('error','Failed to add the authors to the book- did you try and add an author who doesnt exist yet?');
            return redirect()->route('admin.books.create');
        }
        if(!$book->categories()->sync($request->categories)){
            $request->session()->flash('error','Failed to add the categories to the book, does the category you tried to add exist?');
            return redirect()->route('admin.books.create');
        }
        $book->name = $request->name;
        $book->description = $request->description;
        $book->price = $request->price;
        $book->stock = $request->stock;
        if($book->save()){
            $request->session()->flash('success','Book has been updated');
            return redirect()->route('admin.books.index');
        }else{
            $request->session()->flash('error','There was an error updating the user ' . $book->name);
            return redirect()->route('admin.books.index');
        }
        return redirect()->route('admin.books.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Book $book)
    {
        if(Gate::denies('delete-books')){
            return redirect(route('admin.books.index'));
        }
        $book->authors()->detach();
        $book->categories()->detach();
        if($book->delete()){
            $request->session()->flash('success','The book ' .$book->name . ' has been deleted');
        }else{
            $request->session()->flash('error','There was an error deleting the book ' . $book->name);
        }
        return redirect(route('admin.books.index'));
    }
}
