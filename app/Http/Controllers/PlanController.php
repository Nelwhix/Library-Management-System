<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;


class PlanController extends Controller
{
    public function store(Request $request) {
        $fields = $request->validate([
            'plan_name' => 'required|string',
        ]);

        $requestedPlan = Plan::where('name', $fields['plan_name'])->first();

        $user = User::find(auth()->user()->id)->first();

        $currentPlanId = $user->plans->first()->subscription->plan_id;
        $subscriptionRecordId = Subscription::where('user_id', $user->id)->where('plan_id', $currentPlanId)->pluck('id')
            ->first();


        //check whether user has an active subscription
        if (Status::where('name', 'active')->where('statusable_type', 'App\Models\Subscription')->where('statusable_id',
        $subscriptionRecordId)->exists()) {
            return response([
                'message' => 'You already have an active subscription'
            ], 422);
        }

        $newSubscription = Subscription::create([
            'plan_id' => $requestedPlan->id,
            'user_id' => $user->id
        ]);

        Status::create([
           'name' => 'active',
           'description' => 'active entity',
            'statusable_id' => $newSubscription->id,
            'statusable_type' => 'App\Models\Subscription'
        ]);

        return response([
           "message" => "You have successfully purchased the ". $fields['plan_name'] . " plan, it is valid for 30 days"
        ], 200);
    }

    public function index() {
        $allSubs = Subscription::with('status')->where('user_id', auth()->user()->id)->get();

        // remove the active sub from the collection
        $inactiveSubs = $allSubs->filter(function ($allSub) {
            return $allSub->status->name == 'inactive';
        });

        return response([
           'past_subs' => $inactiveSubs,
        ], 200);
    }
}
