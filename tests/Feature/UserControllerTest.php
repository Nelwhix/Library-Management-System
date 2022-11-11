<?php

use App\Models\AccessLevel;
use App\Models\Plan;
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
    $freePlan = Plan::where('name', 'Free')->first();
    $child = AccessLevel::where('name', 'Children')->first();

    $user = User::factory()->create([
        'access_level_id' => $child->id,
        'plan_id' => $freePlan->id,
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

