<?php

namespace App\Http\Controllers\Admin;

use App\Image;
use App\Book;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use Gate;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $images = Image::all();
        return view('admin.images.index')->with('images',$images);
    }
    /**
     * Display form to create a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $books = Book::all();
        return view('admin.images.create')->with('books',$books);
    }
    /**
     * Create a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $bookid = $request->book;
        $book = Book::where('id',$bookid)->get()->first();
        if(!isset($book->name)){
            $request->session()->flash('error','There was a problem finding the book ' . $bookid);
                return redirect()->route('admin.images.create');

        }
        $file = $request->file('fileToUpload'); //id
        $extension = $file->extension();
        $accepted_filetypes = array("jpeg","jpg","png","gif");
        $file_ok=false;

	    //check file type is an accepted type
        foreach($accepted_filetypes as $file_type){ 
            if($extension == $file_type){
                $file_ok = true;
            }
        }
        if($file_ok != true){
            $request->session()->flash('error',"Only the following file types are accepted (.jpeg, .jpg, .png, .gif) - you tried to upload a file of type " . $extension);
            return redirect()->route('admin.images.create');
        }
	    //check file doesnt exist max size
        $maxsize    = 2097152;
        $actualsize = $file->getSize();
        if($actualsize >=$maxsize){
            $request->session()->flash('error','The file you tried to upload exceeded 2MB (the max file size)');
            return redirect()->route('admin.images.create');
        }
        $path = "images";
	    //build file path for front end rendering
        $name  = str_replace(' ', '_', $book->name); // replace spaces with underscores as spaces cause problems on file stores usually
        //remove extension otherwise gets added twice
        $filename =  $name . "_" . $file->getClientOriginalName();//get the files actual name
        $filename = substr($filename, 0, strrpos($filename, '.')); //build

	
        //store file using book_id and file_name as image_name
        if(Storage::disk('s3')->has('images/' . $filename)){ // prevent changing an image that another book has associated with it
            $request->session()->flash('error','The file you tried to upload already exists!');
            return redirect()->route('admin.images.create');
        }
        $newpath = $file->storePubliclyAs($path,$filename,['disk' => 's3']);
        $image = Image::create([
            'name' => $newpath,
            'type' => $extension
        ]);
        $book->images()->attach($image->id);
        $request->session()->flash('success','Image succesfully uploaded');
        return view('admin.images.create')->with(['books' => Book::all(),'path'=>'https://astonbookstore.s3.eu-west-2.amazonaws.com/' . $newpath]); //return path to allow preview of image
    }
}
