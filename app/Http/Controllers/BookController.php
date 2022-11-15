<?php

namespace App\Http\Controllers;

use App\Models\AccessLevel;
use App\Models\AccessLevelBook;
use App\Models\Archive;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->user()->hasRole('author')) {
            return response([
                'message' => 'You are not authorized to add a book'
            ], 403);
        }

        $fields = $request->validate([
            'title' => 'required|string',
            'edition' => 'required|string',
            'description' => 'required|string',
            'prologue' => 'required|string',
            'tags' => 'required|string',
            'categories' => 'required|string',
            'authors' => 'required|string',
            'access_levels' => 'required|string',
        ]);

        $authorArray = explode(",", $fields['authors']);
        $accessArray = explode(',', $fields['access_levels']);

        $book = Book::create([
            'title' => $fields['title'],
            'edition' => $fields['edition'],
            'description' => $fields['description'],
            'prologue' => $fields['prologue'],
            'tags' => $fields['tags'],
            'categories' => $fields['categories'],
        ]);


        foreach($authorArray as $author) {
            $fullName = explode(" ", $author);
            $user = User::where('firstName', $fullName[0])->where('lastName', $fullName[1])->first();

            Archive::create([
               'book_id' => $book->id,
                'user_id' => $user->id,
            ]);
        }

        foreach($accessArray as $accessLevel) {
            $id = AccessLevel::where('name', $accessLevel)->pluck('id')->first();

            AccessLevelBook::create([
                'access_level_id' => $id,
                'book_id' => $book->id,
            ]);
        }

        return response([
           "message" => $fields['title'] . " was successfully added to the Library"
        ], 201);

    }

    public function index(Request $request) {
        $bookRecords = Archive::where('user_id', $request->user()->id)->get();

        foreach ($bookRecords as $record) {
            $allBooks[] = Book::find($record->book_id);
        }

        if ($allBooks === null) {
            return response([
                "message" => 'You don\'t have any uploaded books'
            ], 200);
        }

        return response([
            'books' => $allBooks
        ], 200);
    }

    public function update(Request $request)
    {
        $fields = $request->validate([
            'old_title' => 'required|string',
            'title' => 'string',
            'edition' => 'string',
            'description' => 'string',
            'prologue' => 'string',
            'tags' => 'string',
            'categories' => 'string'
        ]);

        $book = Book::with('users')->where('name', $fields['old_title'])->first();

        // check if author owns book
        $bookRecords = Archive::where('book_id', $book->id)
            ->where('user_id', auth()->user()->id)->get();

        dd($bookRecords);
        if ($book->archive->user_id !== auth()->user()->id) {
            return response([
                "message" => "You do not own this book",
            ], 403);
        }

        $book->update($fields);

        return response([
            "message" => "Book has been successfully updated",
            "book" => $book->fresh(),
        ], 200);
    }
}
