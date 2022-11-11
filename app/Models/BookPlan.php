<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BookPlan extends Pivot
{
    use HasUlids, HasFactory;

    protected $table = "book_plan";
}
