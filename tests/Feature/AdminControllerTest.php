<?php

use App\Models\Plan;
use App\Models\User;

test('admin can add a new plan', function () {
    $user = User::factory()->create()->assignRole('admin');

    $plan = [
        "name" => "Emerald",
        "duration" => "30 days",
        "price" => "100000"
    ];

    $response = $this->actingAs($user, 'web')->post('/api/plans/add', $plan);

    $response->assertStatus(201)->assertJson([
        "message" => $plan['name'] . " added successfully"
    ]);

    $this->assertDatabaseHas('plans', $plan);
})->group('admin');

test('admin can see all plans', function () {
    $user = User::factory()->create()->assignRole('admin');

    $response = $this->actingAs($user, 'web')->get('/api/plans/index');

    $response->assertStatus(200);

})->group('admin');

test('admin can read one plan', function () {
    $user = User::factory()->create()->assignRole('admin');

    $response = $this->actingAs($user, 'web')->post('/api/plans/show', [
        "name" => 'Silver'
    ]);
    $response->assertStatus(200);
})->group('admin');

test('admin can update a plan', function () {
    $user = User::factory()->create()->assignRole('admin');

    $updatedPlan = [
        "plan_name" => 'Silver',
        "name" => "Emerald",
        'duration' => '60 days',
        'price' => '100000'
    ];


    $response = $this->actingAs($user, 'web')->put('/api/plans/update', $updatedPlan);

    $response->assertStatus(200);

    $this->assertDatabaseHas('plans', array_slice($updatedPlan, 1));
})->group('admin');

test('admin can delete a plan', function () {
    $user = User::factory()->create()->assignRole('admin');

    $response = $this->actingAs($user, 'web')->delete('/api/plans/delete', [
        "plan_name" => 'Silver'
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseMissing('plans', [
        'name' => 'Silver'
    ]);
})->group('admin');

test('admin can add an access level', function () {
    $user = User::factory()->create()->assignRole('admin');

    $accessLevel = [
        "name" => "Alien",
        "age" => "150 and above",
        "borrowing_point" => "0"
    ];

    $response = $this->actingAs($user, 'web')->post('/api/accessLevel/add', $accessLevel);

    $response->assertStatus(201)->assertJson([
        "message" => "New access level added"
    ]);

    $this->assertDatabaseHas('accesslevels', $accessLevel);
})->group('admin');

test('admin can see all access levels', function () {
    $user = User::factory()->create()->assignRole('admin');

    $response = $this->actingAs($user, 'web')->get('/api/accessLevel/index');

    $response->assertStatus(200);
})->group('admin');

test('admin can read one access level', function () {
    $user = User::factory()->create()->assignRole('admin');

    $response = $this->actingAs($user, 'web')->post('/api/accessLevel/show', [
        "name" => 'Youth'
    ]);

    $response->assertStatus(200);
})->group('admin');
