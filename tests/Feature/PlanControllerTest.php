<?php

use App\Models\AccessLevel;
use App\Models\Plan;
use App\Models\PlanUser;
use App\Models\Status;
use App\Models\User;

test('user can subscribe to a plan', function () {
    $response = mockUser()->post('/plan/subscribe', [
        'plan_name' => 'Silver'
    ]);

    $response->assertStatus(200)->assertJson([
        "message" => "You have successfully purchased the Silver plan, it is valid for 30 days"
    ]);
})->skip();

test('user can have only one active subscription', function () {
    $youthAccess = AccessLevel::where('name', 'Youth')->first();
    $silverPlan = Plan::where('name', 'Silver')->first();

    $user = User::factory()->create([
        'access_level_id' => $youthAccess->id,
    ]);

    $user->plans()->attach([
        'user_id' => $user->id,
        'plan_id' => $silverPlan->id
    ]);

    $planRecord = PlanUser::where('user_id', $user->id)->where('plan_id', $silverPlan->id)->first();

    Status::factory()->create([
        'name' => 'active',
        'description' => 'active entity',
        'statusable_id' => $planRecord->id,
        'statusable_type' => 'App\Models\PlanUser'
    ]);

    $response = $this->actingAs($user, 'web')->post('/plan/subscribe', [
        'plan_name' => 'Gold'
    ]);

    $response->assertStatus(422)->assertJson([
        'message' => 'You already have an active subscription'
    ]);
})->skip();

test('user can see all past(inactive) subscriptions', function () {
    $youthAccess = AccessLevel::where('name', 'Youth')->first();
    $bronzePlan = Plan::where('name', 'Bronze')->first();

    $user = User::factory()->create([
        'access_level_id' => $youthAccess->id,
    ]);

    $plansId = Plan::pluck('id')->all();
    $user->plans()->attach(

    );


    $response = $this->actingAs($user, 'web')->get('plans/index');

    $response->assertStatus(200);
})->skip();
