<?php

namespace App\Http\Controllers\Order;
use App\Mail\OrderReceipt;
use App\Order;
use App\Book;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Gate;
use Session;

class OrderController extends Controller
{
    public function create(Request $request){
        $user=$request->user();
        $order = Order::where(['user_id'=>$user->id,'purchased'=>false])->orderBy
            ('created_at','desc')->get()->first(); 
        return view('order.create')->with('order',$order);
    }
    public function store(Request $request, Order $order){
    $this->validate($request,['email' => 'required|email|max:255']);
    try{
        $user=$request->user();
        $order = Order::where(['user_id'=>$user->id,'purchased'=>false])->orderBy
            ('created_at','desc')->get()->first();
        if(!$order){
            $request->session()->flash('error','There was a problem completing your purchase! You may have recently' +
            'made a purchase and we havent made you a new basket, please go the home page before rechecking your basket/orders'
            );
            return view('order.create')->with('order',$order);   
        }
        //lower stock accordingly
        foreach($order->books as $book){
            $quantity = $book->pivot->quantity;
            $actual_book = Book::find($book->id);
            $old_stock = $actual_book->stock;
            
            $new_stock=$old_stock - $quantity;
            if($new_stock >= 0){
                $actual_book->stock = $new_stock;
            	
                if($actual_book->save()){
                	//$order->save(); i dont think we would want this as allows partial order completion
                }else{
                	$request->session()->flash('error','There was not enough stock of the book called:' . $book->name);
                return view('order.create')->with('order',$order);  
                }
            }
            else{
                $request->session()->flash('error','There was not enough stock of the book called:' . $book->name);
                return view('order.create')->with('order',$order);  
            }
        }
        //save order
        $order->purchased = 1; //i.e no longer a basket
        if($order->save()){
            if(isset($request->email)){
            	Mail::to($request->email)->queue(new OrderReceipt($user->name,$order));//user email
            	Mail::to(env('MAIL_USERNAME'))->queue(new OrderReceipt($user->name,$order));//audit trail email address for copy of receipt external to above
            }else{
            	//could default to send to the stored email	
            }
	    $orderstr = 'Your order was successfull, your unique reference is ' . $order->id . ' and the total was ' . utf8_encode('£') . $order->total;
            $request->session()->flash('success',$orderstr);
            return redirect()->route('order.showBasket');
        }
    }catch(Exception $ex){ // i dont want the standard error 500 page for orders as its scary particularly as money is involved at this stage in the flow
    	$user=$request->user();
        $order = Order::where(['user_id'=>$user->id,'purchased'=>false])->orderBy
            ('created_at','desc')->get()->first();
    
    	if(isset($order)){
        	$request->session()->flash('error','There was a problem completing your purchase! your basket ID is: ' + $order->id);
        //The above would allow admin or customer services to confirm if the order was placed/ charged for which at this point should be impossible however it helped with debug too
        }else{
    		$request->session()->flash('error','There was a problem completing your purchase!');
        }
        return view('order.create')->with('order',$order);   
    }
         
    }
    public function addToBasket(Request $request,Book $book ){
    if(!Auth::check()){
    	return redirect()->route('login');
    }
        $user=$request->user();
        if($user->id){//logged in
            $order = Order::where(['user_id'=>$user->id,'purchased'=>false])->orderBy
            ('created_at','desc')->get()->first(); 
            if(!$order){//they dont have an existing basket yet
            //need to create an empty basket
                $order = Order::create([
                    'user_id'=>$request->user()->id,
                    'purchased'=>0, //false
                    'total'=>0.0 //no cost yet
                ]);
                //if cant create a basket/order then fail
                if(!$order){
                    $request->session()->flash('error','There was a problem adding to your basket!');
                    return redirect()->route('admin.books.index');
                }
            }
            //at this point an order representing basket is now existing 
            //add the requested book and quantity to the order
            if($book){
                $requested_quantity = $request->quantity;
                $price_of_book=$book->price;
                //check if book already present
                foreach($order->books as $check_book){
                    if($check_book->id === $book->id){
                        $previous_book = $check_book;
                    }
                }
                if(isset($previous_book)){//if they already added then we want to merge
                    $old_quantity = $previous_book->pivot->quantity;
                    $quantity = $request->quantity + $old_quantity;
                	 if($book->stock < $quantity){//quantity more than available stock
                    $request->session()->flash('error','Insufficient stock');
                    	return redirect()->route('admin.books.index');
                	}
                    $order->total = $order->total - ( $old_quantity * $book->price);
                    $order->save();
                    $order->books()->detach($previous_book->id);//to allow new to be added
    
                }else{
                    $quantity = $request->quantity;
                }
                if($book->stock < $quantity){//quantity more than available stock
                    $request->session()->flash('error','Insufficient stock');
                    return redirect()->route('admin.books.index');
                }
                $sub_total= $quantity * $price_of_book;
                if($sub_total <=0 ){
                    $request->session()->flash('error','There was a problem with the evaluated price');
                    return redirect()->route('admin.books.index');
                }
                $order->books()->attach(
                    $book,["quantity"=>$quantity,"sub_total"=>$sub_total]
                );
                $old_total = $order->total;
                $order->total = $old_total + $sub_total;
                if($order->save()){
                    $request->session()->flash('success','Item(s) added to your basket successfully!');
                    return redirect()->route('admin.books.index');
                }
            }else{
                $request->session()->flash('error','Failed to find the book in our database!');
                return redirect()->route('admin.books.index');
            }
        }else{
            $request->session()->flash('error','Failed to add item to your basket, are you logged in?');
            return redirect()->route('admin.books.index');
        }
        
    }
    public function remove(Request $request,int $orderid){
        $order = Order::find($orderid);
        if(isset($request->bookid)){
            $qty = $request->qty;
            $old_total = $order->total;
            $sub_total = $request->sub_total;
            $order->books()->wherePivot('book_id','=',$request->bookid)->detach();
                //remove book then need to update cost
                
                $order->total = ($old_total - $sub_total);
                $order->save();
                $request->session()->flash('success','Removed the book from your basket.');
                return redirect()->route('order.show',$order);
        }else{
            //do nothing
            dd($order);
            $request->session()->flash('error','Failed to remove the book from the order');
                return redirect()->route('order.show',$order);
        }
    }
    public function destroy(Request $request,Order $order){
        
    }
    public function update(Request $request, Order $order){
        foreach($order->books as $book){
            if($book->id == $request->bookid){
            	if($book->stock < $request->quantity){
                	$request->session()->flash('error','Insufficent stock!');
                    return view('order.show')->with('order',$order);
                }
                $old_total = $order->total;
                $new_total = $old_total - $book->pivot->sub_total;
                $new_sub_total = $request->quantity * $book->price;
                $new_total = $new_total + $new_sub_total;
             
                $order->books()->detach($book->id);//remove to refresh
                $order->books()->attach(
                    $book,["quantity"=>$request->quantity,"sub_total"=>$new_sub_total]
                );
                $order->total = $new_total;
                if($order->save()){
                    $request->session()->flash('success','Basket updated!');
                     return redirect()->route('order.show',$order);
                }
            	else{
            		$request->session()->flash('error','Basket failed to update!');
                     return redirect()->route('order.show',$order);
            	}
                
            }
        }
    }
    public function showBasket(Request $request){ // i.e get basket
        $user=$request->user();
        if(($user)){//logged in
            //get basket (if exists)
            $basket = Order::where(['user_id'=>$user->id,'purchased'=>false])->orderBy
            ('created_at','desc')->get()->first(); 
            if(!is_null($basket)){//they already have an incomplete order (basket)
                return view('order.show')->with('order',$basket); // in this case a basket
            }else{ //create an empty basket / order with purchased set to false
                $order = Order::create([
                    'user_id'=>$request->user()->id,
                    'purchased'=>0, //false
                    'total'=>0.0 //no cost yet
                ]);
                return view('order.show')->with('order',$order);
            }
        }
        return redirect()->back();
    }
    public function show(Request $request, Order $order){
        return view('order.show')->with('order',$order);
    }
    
    public function index(Request $request){
        function filter(Request $request){
            if(!isset($request->date_end)){
                $date_end = date('Y-m-d H:i:s');//use current time stamp
            }else{
                $date_end = $request->date_end;
            }
            $user = Auth::user();
        
            $isadmin = $user->hasRole('staff');
            $filter_email = $request->email;
            $filter_user = User::where('email',$filter_email)->first();
            if($filter_user){
                $filter_user_id = $filter_user->id;
            }else{
                $filter_user_id=false;
            }
            $orders = Order::sortable()->where('purchased',1)->when(!$isadmin,function($query,$isadmin) use($user){
                return $query->where('user_id',$user->id);
            });
            
            if(!isset($request->date_end) and !isset($request->date_start)
            and !isset($request->email) and !isset($request->price_lower)
            and !isset($request->price_upper)
           ){
               $orders = Order::sortable()->paginate(5);
               return view('order.index')->with(['orders'=>$orders,'filtered'=>false]);
           }
            $orders= $orders->when($filter_user_id,function($query,$filter_user_id){
                return $query->where('user_id','=',$filter_user_id);
            })->when($request->price_lower,function($query,$price){
                return $query->where('total','<=',$price);
            })->when($request->price_upper,function($query,$price){
                return $query->where('total','>=',$price);
            });
            if($request->date_start){
                $orders->whereBetween('updated_at',[$request->date_start,$date_end]);
            }elseif($request->date_end){
                $orders->where('updated_at','<=',$date_end);
            }
            $orders = $orders->paginate(5);
            return $orders;
        }
        $filtered = false;
        $user = $request->user();
        if(Gate::denies('view-all-orders')){ // then they arent admin so should view all their specific orders
            if(isset($user)){ // if logged in
                if(isset($request->filter)){
                    $orders = filter($request);
                    $filtered=true;
                }else{
                    $orders = Order::sortable()->where(['user_id'=>$user->id,'purchased'=>1
                ])->orderBy
                ('created_at','desc')->paginate(5);
                }
                 //get all orders for that user that arent a "basket"
                return view('order.index')->with(['orders'=>$orders,'filtered'=>$filtered]);
            }
            redirect(route('admin.books.index'));
        }
        else{
        if(isset($user)){ // if logged in
            if(isset($request->filter)){
                $orders = filter($request);
                $filtered=true;
            }else{
                $orders = Order::sortable()->where(['purchased'=>1
            ])->orderBy
            ('created_at','desc')->paginate(5); //get all orders that arent a "basket"
            
            }
            return view('order.index')->with(['orders'=>$orders,'filtered'=>$filtered]);
            
        }
        redirect(route('admin.books.index'));
    }
        
    }
}
