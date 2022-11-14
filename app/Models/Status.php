<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'status';

    protected $fillable = [
        'name',
        'description',
        'statusable_id',
        'statusable_type'
    ];

    public function statusable() {
        return $this->morphTo();
    }

    public function planuser() {
        return $this->morphedByMany(PlanUser::class, 'statusable');
    }
}
