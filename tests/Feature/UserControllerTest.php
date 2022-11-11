<?php

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

   $response = $this->post('/register', [
       'firstName' => 'Nelson',
       'lastName' => 'Isioma',
       'userName' => 'Nelwhix',
       'age' => 22,
       'address' => '4 Asani Street, Bariga Lagos',
       'email' => 'nelsonisioma1@gmail.com',
       'password' => 'admin',
       'password_confirmation' => 'admin',
   ]);
   $response->assertStatus(201);
});

