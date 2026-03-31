<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'student_id',
        'email',
        'password',
        'role',
        'credit_balance'
    ];

    public function information(): HasOne
    {
        return $this->hasOne(UserInformation::class, 'user_id');
    }

    public function studentBalances(): HasMany
    {
        return $this->hasMany(StudentBalance::class, 'user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    protected $hidden = [
        'password'
    ];
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
