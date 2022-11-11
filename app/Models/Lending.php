<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lending extends Model
{
    use HasFactory, HasUlids;


    public function users() {
        return $this->belongsTo(User::class);
    }

    public function books() {
        return $this->belongsTo(Book::class);
    }
}
