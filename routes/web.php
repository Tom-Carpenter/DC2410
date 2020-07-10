<?php
use App\Mail\OrderReceipt;
use Illuminate\Support\Facades\Mail;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function(){
    Route::resource('/books','BookController');
    Route::resource('/users','UserController',['except' => ['show','create','store']]);
    Route::resource('/images','ImageController');
    Route::resource('/authors','AuthorController');
}); 

Route::namespace("Order")->group(function(){
    Route::post('/order/store','OrderController@store')->name('order.create');
    Route::post('/order/create','OrderController@create')->name('order.create');
    Route::post('/order/addToBasket/{book}','OrderController@addToBasket')->name('order.add');
    Route::post('/order/remove/{orderid}','OrderController@remove')->name('order.remove');
    Route::get('/order/showBasket','OrderController@showBasket')->name('order.showBasket');
    Route::resource('/order','OrderController');
});
