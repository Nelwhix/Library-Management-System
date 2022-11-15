<?php

use App\Models\AccessLevel;
use App\Models\Book;
use App\Models\Lending;
use App\Models\Plan;
use App\Models\Status;
use App\Models\User;
use function PHPUnit\Framework\assertEquals;

test("users with wrong access level can't borrow", function () {
    $access_level = AccessLevel::where('name', 'Children')->first();

    // creating a youth book
    $book = Book::factory()->create();
    $bookAccess = AccessLevel::where('name', 'Youth')->first();
    $book->accessLevels()->attach([
        'book_id' => $book->id,
        'access_level_id' => $bookAccess->id
    ]);
    Status::factory()->create([
        'name' => 'available',
        'description' => 'available book',
        'statusable_id' => $book->id,
        'statusable_type' => 'App\Models\Book'
    ]);

    $user = User::factory()->create([
        'access_level_id' => $access_level->id, // this is a child's access level
    ]);

    $response = $this->actingAs($user, 'web')->post('/api/borrow-book', $book->toArray());

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
    Status::factory()->create([
        'name' => 'available',
        'description' => 'available book',
        'statusable_id' => $book->id,
        'statusable_type' => 'App\Models\Book'
    ]);

    // Mock user is a youth user on a free plan
    $response = mockUser()->post('/api/borrow-book', $book->toArray());

    $response->assertStatus(403);
});

test('user can borrow book', function () {
   $response = mockUser()->post('/api/borrow-book', mockBook()->toArray());

   $response->assertStatus(201);
});

test('user cannot borrow unavailable book', function () {
    $youthAccess = AccessLevel::where('name', 'Youth')->first();
    $freePlan = Plan::where('name', 'Free')->first();


    $book = Book::factory()->create();
    $book->plans()->attach([
        'book_id' => $book->id,
        'plan_id' => $freePlan->id
    ]);
    $book->accessLevels()->attach([
        'book_id' => $book->id,
        'access_level_id' => $youthAccess->id
    ]);

    Status::factory()->create([
        'name' => 'borrowed',
        'description' => 'borrowed entity(book)',
        'statusable_id' => $book->id,
        'statusable_type' => 'App\Models\Book'
    ]);

    $response = mockUser()->post('/api/borrow-book', $book->toArray());

    $response->assertStatus(422);
});

test('user can borrow many books', function () {
    // Let's see if user can borrow three books
    $user = mockUser();

    $response1 = $user->post('/api/borrow-book', mockBook()->toArray());
    $response2 = $user->post('/api/borrow-book', mockBook()->toArray());
    $response3 = $user->post('/api/borrow-book', mockBook()->toArray());

    $response1->assertStatus(201);
    $response2->assertStatus(201);
    $response3->assertStatus(201);
});

test('user gets 2 points on book return', function () {
    $book = mockBook();

    $youthAccess = AccessLevel::where('name', 'Youth')->first();
    $user = User::factory()->create([
        'access_level_id' => $youthAccess->id,
    ]);

    $pointsBefore = $user->points;

    Lending::factory()->create([
        'book_id' => $book->id,
        'user_id' => $user->id,
        'date_borrowed' => now()->subDays(6),
        'date_due' => now()->addDays(3),
    ]);

    Status::factory()->create([
        'statusable_id' => $book->id,
        'statusable_type' => 'App\Models\Book'
    ]);

    $response = $this->actingAs($user, 'web')->put('/api/return-book', $book->toArray());

    $response->assertStatus(200)->assertJson([
        "message" => "Book returned successfully, You have gained 2 points"
    ]);

    assertEquals($user->fresh()->points - $pointsBefore, 2);
});

test('user loses a point on late return', function () {
    $book = mockBook();

    $youthAccess = AccessLevel::where('name', 'Youth')->first();
    $user = User::factory()->create([
        'access_level_id' => $youthAccess->id,
    ]);

    $pointsBefore = $user->points;

    Lending::factory()->create([
        'date_borrowed' => now()->subDays(10),
        'date_due' => now()->subDays(3),
        'book_id' => $book->id,
        'user_id' => $user->id,
    ]);

    Status::factory()->create([
        'statusable_id' => $book->id,
        'statusable_type' => 'App\Models\Book'
    ]);

    $response = $this->actingAs($user, 'web')->put('/api/return-book', $book->toArray());

    $response->assertStatus(200)->assertJson([
        "message" => "Book returned successfully, You have lost a point"
    ]);

    assertEquals($pointsBefore - $user->fresh()->points, 1);
});

test('user can see all books he has borrowed', function () {
    $youthAccess = AccessLevel::where('name', 'Youth')->first();
    $user = User::factory()->create([
        'access_level_id' => $youthAccess->id,
    ]);

   $books1 = mockBooks(6);
   $books2 = mockBooks(4);

   // let's make 6 borrowed books and 4 returned books for this user
    foreach ($books1 as $book) {
        Lending::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id
        ]);
    }

    foreach ($books2 as $book) {
        Lending::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'date_returned' => now()
        ]);
    }

    $response = $this->actingAs($user, 'web')->get('/api/borrow/index');

    $response->assertStatus(200)->assertJson([
        "message" => "You have ". count($books1) . " borrowed books",
    ]);
});

test('user can see all books he has returned', function () {
    $youthAccess = AccessLevel::where('name', 'Youth')->first();

    $user = User::factory()->create([
        'access_level_id' => $youthAccess->id,
    ]);

    $books1 = mockBooks(6);
    $books2 = mockBooks(4);

    // let's make 6 borrowed books and 4 returned books for this user
    foreach ($books1 as $book) {
        Lending::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id
        ]);
    }

    foreach ($books2 as $book) {
        Lending::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'date_returned' => now()
        ]);
    }

    $response = $this->actingAs($user, 'web')->get('/api/return/index');

    $response->assertStatus(200)->assertJson([
        "message" => "You have ". count($books2) . " returns",
    ]);
});
