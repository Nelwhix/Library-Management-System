<?php

use App\Models\AccessLevel;
use App\Models\Book;
use App\Models\Plan;
use App\Models\PlanUser;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\PermissionRegistrar;

uses(Tests\TestCase::class, RefreshDatabase::class)->in('Feature');

uses()->beforeEach(function () {
    $this->app->make(PermissionRegistrar::class)->registerPermissions();
    $this->seed();
})->in('Feature');

function mockUser() {
    $youthAccess = AccessLevel::where('name', 'Youth')->first();
    $freePlan = Plan::where('name', 'Free')->first();

    $user = User::factory()->create([
        'access_level_id' => $youthAccess->id,
    ]);

    $user->plans()->attach([
       'user_id' => $user->id,
        'plan_id' => $freePlan->id
    ]);

    return test()->actingAs($user, 'web');
}

function mockBook() {
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
        'name' => 'available',
        'description' => 'available book',
        'statusable_id' => $book->id,
        'statusable_type' => 'App\Models\Book'
    ]);

    return $book;
}

function mockBooks(Int $num) {
    $books = Book::factory()->count($num)->create();

    // attaching a plan, access level and status to each book created
    Book::all()->each(function ($book) {
        $freePlan = Plan::where('name', 'Free')->first();
        $youthAccess = AccessLevel::where('name', 'Youth')->first();

        $book->plans()->attach([
            'book_id' => $book->id,
            'plan_id' => $freePlan->id
        ]);

        $book->accessLevels()->attach([
            'book_id' => $book->id,
            'access_level_id' => $youthAccess->id
        ]);

        Status::factory()->create([
            'name' => 'available',
            'description' => 'available book',
            'statusable_id' => $book->id,
            'statusable_type' => 'App\Models\Book'
        ]);
    });

    return $books;
}


