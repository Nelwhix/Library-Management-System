<?php

use App\Models\AccessLevel;
use App\Models\Book;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\PermissionRegistrar;

uses(Tests\TestCase::class)->in('Feature');

uses()->beforeEach(function () {
    $this->app->make(PermissionRegistrar::class)->registerPermissions();

    Event::listen(MigrationsEnded::class, function () {
        $this->seed();
    });
});

function mockUser($user = null) {
    $youthAccess = AccessLevel::where('name', 'Youth')->first();
    $freePlan = Plan::where('name', 'Free')->first();

    return test()->actingAs($user ?? User::factory()->create([
        'access_level_id' => $youthAccess->id,
        'plan_id' => $freePlan->id,
    ]), 'web');
}

function mockBook($book = null) {
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

    return $book;
}


