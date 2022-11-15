<?php

use App\Models\User;



test('app starts', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('user can register and get token', function () {
    $user = [
        'firstName' => fake()->firstName(),
        'lastName' => fake()->lastName(),
        'userName' => fake()->userName(),
        'age' => fake()->numberBetween(7,100),
        'address' => fake()->address(),
        'points' => fake()->numberBetween(10, 100),
        'email' => fake()->unique()->safeEmail(),
        'password' => '1234',
        'password_confirmation' => '1234'
    ];

   $response = $this->post('/api/register', $user);
   $response->assertStatus(201);
});

test('user can edit profile', function () {
   $response = mockUser()->put('/api/profile/edit', [
       'firstName' => 'Updated firstName',
       'lastName' => 'Updated lastName',
       'userName' => 'Updated userName',
       'age' => 13,
       'address' => 'Updated address'
   ]);

   $response->assertStatus(200);
});

test('user can log out and revoke tokens', function () {
   $response = mockUser()->post('/api/logout');

   $response->assertStatus(200);
});
