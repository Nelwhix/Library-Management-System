<?php

use App\Models\AccessLevel;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Status;
use App\Models\User;

test('user can subscribe to a plan', function () {
    $response = mockUser()->post('/plan/subscribe', [
        'plan_name' => 'Silver'
    ]);

    $response->assertStatus(200)->assertJson([
        "message" => "You have successfully purchased the Silver plan, it is valid for 30 days"
    ]);
});

test('user can have only one active subscription', function () {
    $youthAccess = AccessLevel::where('name', 'Youth')->first();
    $silverPlan = Plan::where('name', 'Silver')->first();

    $user = User::factory()->create([
        'access_level_id' => $youthAccess->id,
    ]);

    $subscription = Subscription::factory()->create([
       'user_id' => $user->id,
       'plan_id' => $silverPlan->id,
    ]);


    Status::factory()->create([
        'name' => 'active',
        'description' => 'active entity',
        'statusable_id' => $subscription->id,
        'statusable_type' => 'App\Models\Subscription'
    ]);

    $response = $this->actingAs($user, 'web')->post('/plan/subscribe', [
        'plan_name' => 'Gold'
    ]);

    $response->assertStatus(422)->assertJson([
        'message' => 'You already have an active subscription'
    ]);
});

test('user can see all past(inactive) subscriptions', function () {
    $youthAccess = AccessLevel::where('name', 'Youth')->first();

    $user = User::factory()->create([
        'access_level_id' => $youthAccess->id,
    ]);

    $plansId = Plan::pluck('id')->all();



    $response = $this->actingAs($user, 'web')->get('plans/index');

    $response->assertStatus(200);
})->skip();
