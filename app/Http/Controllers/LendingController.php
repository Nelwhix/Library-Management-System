<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Lending;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;

class LendingController extends Controller
{
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|string',
            'edition' => 'required|string'
        ]);

        $book = Book::where('title', $fields['title'])->where('edition', $fields['edition'])->first();

        if ($book === null) {
            return response("Book does not exist on our database", 404);
        }

        // TODO Check if book is available



        $user_id = auth()->user()->id;
        $user = User::where('id', $user_id)->first();

        // checking whether user has right access level for book
        $bookAccessLevels = $book->accessLevels;

        if ($bookAccessLevels->find($user->access_level_id) === null) {
            return response("You don't have the right access level to borrow this book", 403);
        }

        // checking whether user has the right plan for book
        $bookPlans = $book->plans;

        if ($bookPlans->find($user->plan_id) === null) {
            return response("You don't have the right plan for this book", 403);
        }

        // Every book must be returned in a week
        Lending::create([
            'book_id' => $book->id,
            'user_id' => $user_id,
            'date_borrowed' => now(),
            'date_due' => now()->addDays(7)
        ]);

        Status::create([
            'name' => 'borrowed',
            'description' => 'borrowed entity(book)',
            'statusable_id' => $book->id,
            'statusable_type' => "App\Models\Book"
        ]);

        return response("Book borrowed successfully, Date due for return is" . " " . now()->addDays(7),
            201);
    }

}
