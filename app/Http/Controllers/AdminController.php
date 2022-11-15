<?php

namespace App\Http\Controllers;

use App\Models\AccessLevel;
use App\Models\Plan;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function plan_store(Request $request) {
        if (!$request->user()->hasRole('admin')) {
            return response([
                'message' => 'You are not permitted'
            ], 403);
        }

        $fields = $request->validate([
            'name' => 'required|string',
            'duration' => 'required|string',
            'price' => 'required|integer',
        ]);

        $plan = Plan::create([
           'name' => $fields['name'],
           'duration' => $fields['duration'],
            'price' => $fields['price']
        ]);

        return response([
            'message' => $plan['name'] . " added successfully"
        ], 201);
    }

    public function plan_index() {
        return response([
            'plans' => Plan::all()
        ], 200);
    }

    public function plan_show(Request $request) {
        $fields = $request->validate([
           'name' => 'required|string'
        ]);

        return response([
            'plan' => Plan::where('name', $fields['name'])->first()
        ], 200);
    }

    public function plan_update(Request $request) {
        if (!$request->user()->hasRole('admin')) {
            return response([
                'message' => 'You are not permitted'
            ], 403);
        }

        $fields = $request->validate([
            'plan_name' => 'string',
           'name' => 'string',
            'duration' => 'string',
            'price' => 'string'
        ]);

        $plan = Plan::where('name', $fields['plan_name'])->first();

        $plan->update($fields);

        return response([
            "message" => "Plan updated successfully"
        ], 200);
    }

    public function plan_destroy(Request $request) {
        if (!$request->user()->hasRole('admin')) {
            return response([
                'message' => 'You are not permitted'
            ], 403);
        }

        $fields = $request->validate([
            'plan_name' => 'required|string'
        ]);

        $plan = Plan::where('name', $fields['plan_name'])->first();
        $plan->delete();

        return response([
            "Plan deleted successfully"
        ], 200);
    }

    public function access_level_store(Request $request) {
        if (!$request->user()->hasRole('admin')) {
            return response([
                'message' => 'You are not permitted'
            ], 403);
        }

        $fields = $request->validate([
           'name' => 'required|string',
            'age' => 'required|string',
            'borrowing_point' => 'required|integer'
        ]);

        AccessLevel::create([
            'name' => $fields['name'],
            'age' => $fields['age'],
            'borrowing_point' => $fields['borrowing_point']
        ]);

        return response([
            "message" => "New access level added"
        ], 201);
    }

    public function access_level_index() {
        return response([
            'accessLevels' => AccessLevel::all()
        ], 200);
    }

    public function access_level_show(Request $request) {
        $fields = $request->validate([
           'name' => 'required|string'
        ]);

        return response([
            "access_level" => AccessLevel::where('name', $fields['name'])->first()
        ], 200);
    }
}
