<?php

use App\Models\AccessLevel;
use App\Models\Book;
use App\Models\Plan;
use App\Models\User;

test("users with wrong access level can't borrow", function () {
    $access_level = AccessLevel::where('name', 'Children')->first();

    // creating a youth book on silver plan
    $book = Book::factory()->create();
    $bookAccess = AccessLevel::where('name', 'Youth')->first();
    $book->accessLevels()->attach([
        'book_id' => $book->id,
        'access_level_id' => $bookAccess->id
    ]);
    $bookPlan = Plan::where('name', 'Silver')->first();
    $book->plans()->attach([
        'book_id' => $book->id,
        'plan_id' => $bookPlan->id
    ]);

    $user = User::factory()->create([
        'access_level_id' => $access_level->id, // this is a child's access level on silver plan
        'plan_id' => $bookPlan->id,
    ]);

    $response = $this->actingAs($user, 'web')->post('/borrow-book', $book->toArray());

    $response->assertStatus(403);
});


test("users with wrong plan can't borrow", function () {
    $freePlan = Plan::where('name', 'Free')->first();

    // creating a book on silver plan and youth access level
    $book = Book::factory()->create();
    $bookPlan = Plan::where('name', 'Silver')->first();
    $book->plans()->attach([
        'book_id' => $book->id,
        'plan_id' => $bookPlan->id
    ]);
    $bookAccess = AccessLevel::where('name', 'Youth')->first();
    $book->accessLevels()->attach([
        'book_id' => $book->id,
        'access_level_id' => $bookAccess->id
    ]);

    $user = User::factory()->create([
        'plan_id' => $freePlan->id, // this is a user on free plan and youth access level
        'access_level_id' => $bookAccess->id,
    ]);

    $response = $this->actingAs($user, 'web')->post('/borrow-book', $book->toArray());

    $response->assertStatus(403);
});

test('user can borrow book', function () {
    $access_level = AccessLevel::where('name', 'Youth')->first();

    $user = User::factory()->create([
       'access_level_id' => $access_level->id,
   ]);


   $response = $this->actingAs($user, 'web')->post('/borrow-book', [
       "title" => 'Pariatur qui alias non neque.',
       'edition' => '2nd Edition',
   ]);

   $response->assertStatus(201);
})->skip();
