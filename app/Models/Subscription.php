<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Subscription extends Pivot
{
    use HasUlids, HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'plan_id',
        'user_id',
    ];

    public function status() {
        return $this->morphOne(Status::class, 'statusable');
    }
}
