<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lending extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
      'book_id',
      'user_id',
      'date_borrowed',
        'date_due',
        'date_returned'
    ];

    public function users() {
        return $this->belongsTo(User::class);
    }

    public function books() {
        return $this->belongsTo(Book::class);
    }
}
