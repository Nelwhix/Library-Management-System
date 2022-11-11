<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $this->app->make(PermissionRegistrar::class)->registerPermissions();

    Event::listen(MigrationsEnded::class, function () {
        $this->artisan('db:seed', ['--class' => PermissionSeeder::class]);
    });
});

test('app starts', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('user can register and get token', function () {
    $user = User::factory()->make([
        'password_confirmation' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    ]);

   $response = $this->post('/register', $user->toArray());
   $response->assertStatus(201);
});

test('user can edit profile', function () {
    $user = User::factory()->create([
        'access_level_id' => '01GHKERF8KBC04B6R8F94RC3D0',
    ]);

   $response = $this->actingAs($user, 'web')->put('/profile/edit', [
       'firstName' => 'Updated firstName',
       'lastName' => 'Updated lastName',
       'userName' => 'Updated userName',
       'age' => 13,
       'address' => 'Updated address'
   ]);

   $response->assertStatus(200);
});

