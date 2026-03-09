<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'email',
        'password',
        'role',
    ];

    public function information(): HasOne
    {
        return $this->hasOne(UserInformation::class, 'user_id');
    }

    public function studentBalances(): HasMany
    {
        return $this->hasMany(StudentBalance::class, 'user_id');
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
