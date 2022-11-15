<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Status;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $book = Book::factory()->create([
           'title' => 'Half of a Yellow Sun',
            'edition' => '1st Edition'
        ]);

        Status::factory()->create([
           'name' => 'available',
            'description' => 'available entity(book)',
            'statusable_id' => $book->id,
            'statusable_type' => 'App\Models\Book'
        ]);
    }
}
