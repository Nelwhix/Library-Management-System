<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Lending;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

        // check if book is available
        if ($book->status->name === "borrowed") {
            return response("Book is currently unavailable", 403);
        }

        $user = User::find(auth()->user()->id);

        // checking whether user has right access level for book
        $bookAccessLevels = $book->accessLevels;

        if ($bookAccessLevels->find($user->access_level_id) === null) {
            return response("You don't have the right access level to borrow this book", 403);
        }

        // getting all the plans for a book
        $bookPlans = $book->plans;
        $userPlan = $user->plans->first();

        foreach($bookPlans as $plan) {
            $bookPlanArray[] = $plan->pivot->plan_id;
        }

        $userPlanId = $userPlan->pivot->plan_id;
        // checking whether user has the right plan for book
        if (!in_array($userPlanId, $bookPlanArray)) {
            return response("You don't have the right plan for this book", 403);
        }

        // Every book must be returned in a week
        Lending::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
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

    public function update(Request $request) {
        $fields = $request->validate([
           'title' => 'required|string',
           'edition' => 'required|string',
        ]);

        $book = Book::with('lendings')->where('title', $fields['title'])
            ->where('edition', $fields['edition'])->first();

        $user = User::find(auth()->user()->id);
        $timeDifference = Carbon::create($book->lendings->date_due)->diffInDays(now());

        if ($timeDifference > 0) {
            $user->points -= 1;
            $points = -1;
        } else {
            $user->points += 2;
            $points = 2;
        }
        $user->save();

        // make the book available again
        $book->status->name = "available";
        $book->status->description = "available book";
        $book->status->save();

        if ($points > 0) {
            return response([
                "message" => "Book returned successfully, You have gained " . $points. " points"
            ], 200);
        } else {
            return response([
                "message" => "Book returned successfully, You have lost a point"
            ], 200);
        }
    }

    public function index() {
        $lendings = Lending::where('user_id', auth()->user()->id)->get();

        // remove lendings that have been returned
        $filtered = $lendings->reject(function($lending) {
           return $lending->date_returned;
        });

        return response([
            "message" => "You have ". count($filtered->all()) . " borrowed books",
            "books" => $filtered->all()
        ], 200);
    }

    public function returnindex() {
        $lendings = Lending::where('user_id', auth()->user()->id)->get();

        // remove lendings that are not yet returned
        $filtered = $lendings->reject(function($lending) {
            return !$lending->date_returned;
        });

        return response([
            "message" => "You have ". count($filtered->all()) . " borrowed books",
            "books" => $filtered->all()
        ], 200);
    }
}
