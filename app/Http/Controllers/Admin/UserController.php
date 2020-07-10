<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\User;
use App\Order;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        if(Gate::denies('edit-users')){
            return redirect()->route('admin.books.index');
        }
        $users = $user->sortable()->paginate(5); //to allow pagination at front end
        return view('/admin.users.index')->with('users',$users);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if(Gate::denies('edit-users')){
            return redirect(route('admin.users.index'));
        }
        $roles = Role::all();
        return view('admin.users.edit')->with(['user'=>$user,'roles'=>$roles]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
	if(Gate::denies('edit-users')){
            return redirect(route('admin.users.index'));
        } // reject unauthorised modifcation of users 
	$this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255']
        ]);
        if(isset($request->roles)){ // this is to prevent the sync message from silently failing (if a user manually changes the id of role at the front end)
            foreach($request->roles as $role){
                if(!Role::where('id','=',$role)->first()){
                    $request->session()->flash('error','The role you tried to assign is not an approved role.');
                    return redirect()->route('admin.users.index');
                }
            }
        }

        $user->roles()->sync($request->roles);
        $user->name = $request->name;
        $user->email = $request->email;
        if($user->save()){
            $request->session()->flash('success','User has been updated');
        }else{
            $request->session()->flash('error','There was an error updating the user ' . $user->name);
        }
        
        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        if(Gate::denies('delete-users')){
            return redirect(route('admin.users.index'));
        }
	if(isset(Order::where('user_id','=',$user->id)->first()->id)){ // prevent SQL foreign key constraint error (between users and user_order and orders)
		$request->session()->flash('error','The user has placed an order has an active basket so could not be deleted');
		return redirect()->route('admin.users.index');
	}
	$roles = $user->roles();
        if(!$user->roles()->detach() and count($user->roles)>0){
		$request->session()->flash('error','There was an error deleting the user ' . $user->name . " we couldn't remove their roles");
		return redirect()->route('admin.users.index');	
	}else{
		if($user->delete()){
            		$request->session()->flash('success','The user ' .$user->name . ' has been deleted');
        	}else{
				$user->roles()->attach($roles); // reattach roles as to revert back to previous state before transaction started /failed
           		 $request->session()->flash('error','There was an error deleting the user ' . $user->name);
        	}
	}
        
        return redirect()->route('admin.users.index');
    }
}
