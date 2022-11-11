<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLevel extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'accesslevels';

    public function users() {
        return $this->hasMany(User::class);
    }

    public function books() {
        return $this->belongsToMany(Book::class);
    }
}
