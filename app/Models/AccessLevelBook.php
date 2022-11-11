<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AccessLevelBook extends Pivot
{
    use HasFactory, HasUlids;

    protected $table = 'access_level_book';
}
