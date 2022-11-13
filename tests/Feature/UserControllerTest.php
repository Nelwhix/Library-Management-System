<?php

use App\Models\User;



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
   $response = mockUser()->put('/profile/edit', [
       'firstName' => 'Updated firstName',
       'lastName' => 'Updated lastName',
       'userName' => 'Updated userName',
       'age' => 13,
       'address' => 'Updated address'
   ]);

   $response->assertStatus(200);
});

test('user can log out and revoke tokens', function () {
   $response = mockUser()->post('/logout');

   $response->assertStatus(200);
});
