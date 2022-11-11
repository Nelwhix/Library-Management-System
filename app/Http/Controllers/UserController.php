<?php

namespace App\Http\Controllers;

use App\Models\AccessLevel;
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

        // PHP8 syntax
        $access_level = match(true) {
            $fields['age'] >= 7 && $fields['age'] < 15 => AccessLevel::where('age', "7 to 15")->first(),

            $fields['age'] >= 15 && $fields['age'] <= 24 => AccessLevel::where('age', "15 to 24")->first(),

            $fields['age'] >= 25 && $fields['age'] <= 49  => AccessLevel::where('age', "25 to 49")->first(),

            $fields['age'] >= 50 => AccessLevel::where('age', "50 and above")->first(),
        };

        dd($access_level);
        $access_level_id = $access_level->id;

        $user = User::create([
            'firstName' => $fields['firstName'],
            'lastName' => $fields['lastName'],
            'userName' => $fields['userName'],
            'email' => $fields['email'],
            'age' => $fields['age'],
            'address' => $fields['address'],
            'password' => bcrypt($fields['password']),
            'access_level_id' => $access_level_id,
            'points' => 10,
        ])->assignRole('reader');

        $token = $user->createToken('appToken')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 201);

    }
}
