<?php

namespace App\Http\Controllers;

use App\Models\AccessLevel;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $fields = $request->validate([
           'firstName' => 'required|string',
           'lastName' => 'required|string',
           'userName' => 'required|string',
           'email' => 'required|string|unique:users,email',
            'age' => 'required|integer',
            'address' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        // Assign user an access level and free plan
        $access_level = $this->accessQuery($fields['age']);
        $plan = Plan::where('name', 'Free')->first();

        $user = User::create([
            'firstName' => $fields['firstName'],
            'lastName' => $fields['lastName'],
            'userName' => $fields['userName'],
            'email' => $fields['email'],
            'age' => $fields['age'],
            'address' => $fields['address'],
            'password' => bcrypt($fields['password']),
            'access_level_id' => $access_level->id,
            'plan_id' => $plan->id,
            'points' => 0,
        ])->assignRole('reader');

        $token = $user->createToken('appToken')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 201);

    }

    public function update(Request $request) {
        $fields = $request->validate([
            'firstName' => 'string',
            'lastName' => 'string',
            'userName' => 'string',
            'age' => 'integer',
            'address' => 'string',
        ]);

        if ($request->has('age')) {
            $access_level = $this->accessQuery($fields['age']);

            $fields['access_level_id'] = $access_level->id;;
        }

        $user = User::where('id', auth()->user()->id)->first();

        $user->update($fields);

        return response([
            'message' => 'Profile edited successfully',
            'user' => $user
        ], 200);
    }

    private function accessQuery(int $age) {
        return match(true) {
            $age >= 7 && $age < 15 => AccessLevel::where('age', "7 to 15")->first(),

            $age >= 15 && $age <= 24 => AccessLevel::where('age', "15 to 24")->first(),

            $age >= 25 && $age <= 49  => AccessLevel::where('age', "25 to 49")->first(),

            $age >= 50 => AccessLevel::where('age', "50 and above")->first(),
        };
    }
}
