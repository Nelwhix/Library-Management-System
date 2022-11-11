<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LendingController extends Controller
{
    public function store(Request $request)
    {
        $fields = $request->validate([
            'book name' => 'required|string'
        ]);


    }
}
