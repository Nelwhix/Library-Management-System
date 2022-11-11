<?php

namespace Database\Seeders;

use App\Models\AccessLevel;
use App\Models\Book;
use App\Models\Plan;
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
        Book::factory()->count(100)->create();

        $accessLevels = AccessLevel::all();

        Book::all()->each(function ($book) use ($accessLevels) {
            $book->accessLevels()->attach(
              $accessLevels->random(rand(1,3))->pluck('id')->toArray()
            );
        });

        $plans = Plan::all();
        Book::all()->each(function ($book) use ($plans) {
            $book->plans()->attach(
                $plans->random(rand(1,3))->pluck('id')->toArray()
            );
        });
    }
}
