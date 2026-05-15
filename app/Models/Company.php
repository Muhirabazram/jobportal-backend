<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['user_id', 'company_name', 'address', 'location', 'phone', 'website', 'logo', 'description', 'industry', 'employee_count'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function jobListings() {
        return $this->hasMany(JobListing::class);
    }
}
