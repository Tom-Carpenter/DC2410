<?php

use Illuminate\Database\Seeder;
use App\Book;
use App\Author;
class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Book::truncate();
        DB::table('book_author')->truncate();
    }
}
