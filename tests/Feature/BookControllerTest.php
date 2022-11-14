<?php

use App\Models\AccessLevel;
use App\Models\Archive;
use App\Models\Book;
use App\Models\User;
use Database\Factories\UserFactory;

test("user without author role cannot add book", function () {
    $response = mockUser()->post('/book/add', mockBook()->toArray());

    $response->assertStatus(403)->assertJson([
        'message' => 'You are not authorized to add a book'
    ]);
});

test('author can add a new book', function () {
    $authors = [
      [
          "firstName" => "Chinua",
          "lastName" => "Achebe",
      ],
      [
          "firstName" => "Wole",
          "lastName" => "Soyinka",
      ],
        [
            "firstName" => "Chimamanda",
            "lastName" => "Adichie",
        ]
    ];

    $accessLevelId = AccessLevel::where('name', "Youth")->pluck('id')->first();
    foreach ($authors as $author) {
        User::factory()->create([
           'firstName' => $author['firstName'],
           'lastName' => $author['lastName'],
            'access_level_id' => $accessLevelId
        ]);
    }

    $book = [
        'title' => 'Purple Hibiscus',
        'edition' => '1st Edition',
        'description' => fake()->paragraph,
        'prologue' => fake()->sentence,
        'tags' => implode(" ,", fake()->words()),
        'categories' => implode(" ,", fake()->words(5)),
        'authors' => 'Chinua Achebe,Wole Soyinka,Chimamanda Adichie'
    ];

    $response = mockAuthor()->post('/book/add', $book);
    $response->assertStatus(201)->assertJson([
        'message' => $book['title'] . " was successfully added to the Library",
    ]);
});

test('author can see his own books', function () {
   $author = User::factory()->create([
      'access_level_id' => AccessLevel::where('name', 'Youth')->pluck('id')->first()
   ]);

   $authorBooks = Book::factory()->count(10)->create();

   foreach ($authorBooks as $book) {
       Archive::factory()->create([
           'user_id' => $author->id,
            'book_id' => $book->id
       ]);
   }

   $response = $this->actingAs($author, 'web')->get('/books/index');

   $response->assertStatus(200);
})->only();

test('author can update his book', function () {
    $user = User::factory()
        ->hasBooks()
        ->create();


    $updates = Book::factory()->makeOne([
        'old_title' => $user->book->title,
    ]);

    $response = mockUser()->put('/books/update', $updates->toArray());

    $response->assertStatus(403);
})->only();
