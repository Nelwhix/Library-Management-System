<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory, HasUlids;

    public function lendings() {
        return $this->hasOne(Lending::class);
    }

    public function plans() {
        return $this->belongsToMany(Plan::class);
    }

    public function accesslevels() {
        return $this->belongsToMany(AccessLevel::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function status() {
        return $this->morphOne(Status::class, 'statusable');
    }
}
