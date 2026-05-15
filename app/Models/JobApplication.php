<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = ['job_listing_id', 'user_id', 'status', 'cover_letter', 'resume', 'experience', 'rejection_reason'];

    public function jobListing() {
        return $this->belongsTo(JobListing::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}
