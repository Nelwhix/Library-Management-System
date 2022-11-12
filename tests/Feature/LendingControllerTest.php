<?php

use App\Models\AccessLevel;
use App\Models\Book;
use App\Models\Lending;
use App\Models\Plan;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Carbon;

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

    // Mock user is a youth user on a free plan
    $response = mockUser()->post('/borrow-book', $book->toArray());

    $response->assertStatus(403);
});

test('user can borrow book', function () {
   $response = mockUser()->post('/borrow-book', mockBook()->toArray());

   $response->assertStatus(201);
});

test('user cannot borrow unavailable book', function () {
    $youthAccess = AccessLevel::where('name', 'Youth')->first();
    $freePlan = Plan::where('name', 'Free')->first();

    $user = User::factory()->create([
        'access_level_id' => $youthAccess->id,
        'plan_id' => $freePlan->id,
    ]);

//    $book = Book::factory()->create();
//    $book->plans()->attach([
//        'book_id' => $book->id,
//        'plan_id' => $freePlan->id
//    ]);
//    $book->accessLevels()->attach([
//        'book_id' => $book->id,
//        'access_level_id' => $youthAccess->id
//    ]);

    // creating an unavailable book
    $book = Book::where('id', '01ghmenmhhxrjh84j0a44mzfaf')->first();


    $response = $this->actingAs($user, 'web')->post('/borrow-book', $book->toArray());

    $response->assertStatus(201);
})->skip();

test('user can borrow many books', function () {
    // Let's see if user can borrow three books
    $user = mockUser();

    $response1 = $user->post('/borrow-book', mockBook()->toArray());
    $response2 = $user->post('/borrow-book', mockBook()->toArray());
    $response3 = $user->post('/borrow-book', mockBook()->toArray());

    $response1->assertStatus(201);
    $response2->assertStatus(201);
    $response3->assertStatus(201);
});

test('user gets 2 points on book return', function () {
    $book = mockBook();
    $youthAccess = AccessLevel::where('name', 'Youth')->first();
    $freePlan = Plan::where('name', 'Free')->first();

    $user = User::factory()->create([
        'access_level_id' => $youthAccess->id,
        'plan_id' => $freePlan->id,
    ]);

    Lending::factory()->create([
        'book_id' => $book->id,
        'user_id' => $user->id,
        'date_borrowed' => Carbon::now()->subDays(6),
        'date_due' => Carbon::now()->addDay(),
    ]);

    Status::factory()->create([
        'statusable_id' => $book->id,
        'statusable_type' => 'App\Models\Book'
    ]);

    $response = $this->actingAs($user, 'web')->put('/return-book', $book->toArray());

    $response->assertStatus(200)->assertJson([
        "message" => "Book returned successfully, You have gained 2 point(s)"
    ]);
})->skip();

test('user loses a point on late return', function () {
    $book = mockBook();
    $youthAccess = AccessLevel::where('name', 'Youth')->first();
    $freePlan = Plan::where('name', 'Free')->first();

    $user = User::factory()->create([
        'access_level_id' => $youthAccess->id,
        'plan_id' => $freePlan->id,
    ]);

    Lending::factory()->create([
        'book_id' => $book->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user, 'web')->put('/return-book', $book->toArray());

    $response->assertStatus(200);

    $response->assertStatus(200)->assertJson([
        "message" => "Book returned successfully, You have lost a point"
    ]);
})->skip();
