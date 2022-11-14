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

    $bronzePlanId = Plan::where('name', 'Bronze')->pluck('id')->first();
    // creating some past subscriptions
    $subscriptions1 = Subscription::factory()->count(10)->create([
        'plan_id' => $bronzePlanId,
        'user_id' => $user->id
    ]);

    foreach($subscriptions1 as $subscription) {
        Status::factory()->create([
           'name' => 'inactive',
           'description' => 'inactive_entity',
            'statusable_id' => $subscription->id,
            'statusable_type' => 'App\Models\Subscription'
        ]);
    }

    $response = $this->actingAs($user, 'web')->get('plans/index');

    $response->assertStatus(200);
});
