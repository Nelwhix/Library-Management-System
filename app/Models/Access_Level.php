<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access_Level extends Model
{
    use HasFactory, HasUlids;

    public function users() {
        return $this->hasMany(User::class);
    }

    public function books() {
        return $this->belongsToMany(Book::class);
    }
}
