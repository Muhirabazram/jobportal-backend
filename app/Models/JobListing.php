<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    protected $fillable = [
        'company_id', 'category_id', 'job_type_id', 'title', 'slug',
        'description', 'requirements', 'location', 'salary_min', 'salary_max', 'is_active'
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }
    public function category() {
        return $this->belongsTo(Category::class);
    }
    public function jobType() {
        return $this->belongsTo(JobType::class);
    }
    public function applications() {
        return $this->hasMany(JobApplication::class);
    }
}
