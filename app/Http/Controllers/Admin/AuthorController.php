<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Author;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthorController extends Controller
{
    /**
     * Return view to use to create an Author
     */
    public function create(Request $request)
    {
        $authors = Author::orderBy('name','ASC')->get();
        return view('admin.authors.create')->with('authors',$authors);
    }

    /**
     * Takes a request with name and creates an author with said name, returning the user to authors creation page, or if the 
     */
    public function store(Request $request){
        $this->validate($request,['name'=>'required|max:250']);
        $redirect_route = 'admin.authors.create';
        if(isset($request->book)){ //allows the book creation pages to utilise this route but return to correct section of website
            if($request->book){
                $redirect_route = 'admin.books.create';
            }
        }
        if(isset($request->name)){
            if(Author::create(['name'=>$request->name])){
                $request->session()->flash('success','You succesfully added ' . $request->name . ' to the list of authors!');
                return redirect()->route($redirect_route);
            }else{
                $request->session()->flash('error','We could not add ' . $request->name . ' to the list of authors!');
                return redirect()->route($redirect_route);
            }
        }
        $request->session()->flash('error','Something went wrong!');
        return redirect()->route($redirect_route);
    }
    /**
     * Show all authors
     */
    public function index(Request $request)
    {
        $authors = Author::orderBy('name','ASC')->get();
        return view('admin.authors.index')->with('authors',$authors);
    }
}
