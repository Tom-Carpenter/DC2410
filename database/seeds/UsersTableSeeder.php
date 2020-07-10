<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Role;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        DB::table('role_user')->truncate();
        $adminRole = Role::where('name','admin')->first();
        $authorRole = Role::where('name','author')->first();
        $userRole = Role::where('name','user')->first();

        $admin = User::create(['name'=>'adminUser','email'=>'admin@admin.com','password'=>Hash::make('admin')]);
        $author = User::create(['name'=>'authorUser','email'=>'author@author.com','password'=>Hash::make('author')]);
        $user = User::create(['name'=>'userUser','email'=>'user@user.com','password'=>Hash::make('user')]);

        $admin->roles()->attach($adminRole);
        $author->roles()->attach($authorRole);
        $user->roles()->attach($userRole);
    }
}
