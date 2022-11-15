<?php

use App\Models\AccessLevel;
use App\Models\Archive;
use App\Models\Book;
use App\Models\User;

test("user without author role cannot add book", function () {
    $response = mockUser()->post('/api/book/add', mockBook()->toArray());

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
        'authors' => 'Chinua Achebe,Wole Soyinka,Chimamanda Adichie',
        'access_levels' => 'Youth,Youth Exclusive,Senior'
    ];

    $response = mockAuthor()->post('/api/book/add', $book);
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

   $response = $this->actingAs($author, 'web')->get('/api/books/index');

   $response->assertStatus(200);
});

test('author can update his book', function () {
    $user = User::factory()
        ->hasBooks(1)
        ->create();

    Archive::factory()->create([
       'user_id' => $user->id,
       'book_id' => $user->books->first()->id,
    ]);

    $updates = Book::factory()->makeOne([
        'old_title' => $user->books->first()->title,
        'title' => 'Updated title',
        'edition' => '2nd Edition',
        'description' => fake()->sentence(),
        'prologue' => fake()->sentence(),
        'tags' => fake()->sentence(),
        'categories' => fake()->sentence()
    ]);
    $response = $this->actingAs($user, 'web')->put('/api/books/update', $updates->toArray());

    $response->assertStatus(200);
    $this->assertDatabaseHas('books', $updates->toArray());
})->skip();
