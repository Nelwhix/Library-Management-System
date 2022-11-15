<?php

namespace Database\Seeders;

use App\Models\AccessLevel;
use App\Models\Book;
use App\Models\Plan;
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

       $book->accesslevels()->attach([
           'access_level_id' => AccessLevel::where('name', 'Youth')->pluck('id')->first()
       ]);

        $book->plans()->attach([
            'plan_id' => Plan::where('name', 'Free')->pluck('id')->first()
        ]);

        Status::factory()->create([
           'name' => 'available',
            'description' => 'available entity(book)',
            'statusable_id' => $book->id,
            'statusable_type' => 'App\Models\Book'
        ]);
    }
}
