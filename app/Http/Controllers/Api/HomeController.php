<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function stats()
    {
        return response()->json([
            'job_count' => JobListing::where('is_active', true)->count(),
            'company_count' => Company::count(),
            'candidate_count' => User::where('role', 'job_seeker')->count(),
        ]);
    }
}