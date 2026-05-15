<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'jabatan',
        'phone',
        'location',
        'bio',
        'avatar',
        'cv_path'
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

    /* avatar url */
    protected $appends = ['avatar_url', 'cv_url'];

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    public function getCvUrlAttribute()
    {
        return $this->cv_path ? asset('storage/' . $this->cv_path) : null;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke Company (untuk employer)
     */
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    /**
     * Relasi ke JobApplication (lamaran dari job seeker)
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Relasi ke SavedJob (lowongan yang disimpan)
     */
    public function savedJobs()
    {
        return $this->hasMany(SavedJob::class);
    }
}