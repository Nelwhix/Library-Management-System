<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\PlanUser;
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
        $userPlanId = $user->plans->first()->pivot->plan_id;
        $planRecordId = PlanUser::where('user_id', $user->id)->where('plan_id', $userPlanId)->pluck('id')->first();


        //check whether user has an active subscription
        if (Status::where('name', 'active')->where('statusable_type', 'App\Models\PlanUser')->where('statusable_id',
        $planRecordId)->exists()) {
            return response([
                'message' => 'You already have an active subscription'
            ], 422);
        }

        $planRecord = PlanUser::where('plan_id', $userPlanId)->where('user_id', $user->id)->first();

        $planRecord->plan_id = $requestedPlan->id;

        $planRecord->save();

        Status::create([
           'name' => 'active',
           'description' => 'active entity',
            'statusable_id' => $planRecord->id,
            'statusable_type' => 'App\Models\PlanUser'
        ]);

        return response([
           "message" => "You have successfully purchased the ". $fields['plan_name'] . " plan, it is valid for 30 days"
        ], 200);
    }

    public function index() {
        $allUsersPlans = PlanUser::where('user_id', auth()->user()->id)->get();

        dd($allUsersPlans);
    }
}
