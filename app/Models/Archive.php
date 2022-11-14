<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Archive extends Pivot
{
    use HasUlids, HasFactory;

    protected $table = 'archives';

    protected $fillable = [
        'book_id',
        'user_id'
    ];
}
