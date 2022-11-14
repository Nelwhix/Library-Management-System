<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'userName',
        'email',
        'password',
        'age',
        'address',
        'points',
        'access_level_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function lendings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lending::class);
    }

    public function accesslevels() {
        return $this->belongsTo(AccessLevel::class);
    }

    public function books() {
        return $this->belongsToMany(Book::class);
    }

    public function status() {
        return $this->morphOne(Status::class, 'statusable');
    }

    public function plans() {
        return $this->belongsToMany(Plan::class)->using(PlanUser::class);
    }
}
