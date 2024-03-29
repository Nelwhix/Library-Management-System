<?php

namespace App\Http\Controllers;

use App\Models\AccessLevel;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        $access_level = $this->accessQuery($fields['age']);
        $freePlan = Plan::where('name', 'Free')->first();

        $user = User::create([
            'firstName' => $fields['firstName'],
            'lastName' => $fields['lastName'],
            'userName' => $fields['userName'],
            'email' => $fields['email'],
            'age' => $fields['age'],
            'address' => $fields['address'],
            'password' => Hash::make($fields['password']),
            'access_level_id' => $access_level->id,
            'points' => 0,
        ])->assignRole('reader');

        Subscription::create([
           'user_id' => $user->id,
           'plan_id' => $freePlan->id,
        ]);

        return response([
            'user' => $user
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

    public function login(Request $request) {
        $fields = $request->validate([
           'email' => 'required|string',
           'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
               'message' => 'Bad credentials'
            ], 401);
        }

        $token = $user->createToken('myappToken')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout() {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'tokens revoked'
        ]);
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
