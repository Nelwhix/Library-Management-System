<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PlanUser extends Pivot
{
    use HasUlids;

    protected $table = 'plan_user';

    protected $fillable = [
        'plan_id',
        'user_id',
    ];

    public function status() {
        return $this->morphToMany(Status::class, 'statusable');
    }
}
