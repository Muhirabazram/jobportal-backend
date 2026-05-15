<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Untuk pencari kerja
    public function jobSeekerStats(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'applied_count' => $user->applications()->count(),
            'saved_count' => $user->savedJobs()->count(),
            'pending_count' => $user->applications()->where('status', 'pending')->count(),
            'reviewed_count' => $user->applications()->where('status', 'reviewed')->count(),
        ]);
    }

    // Untuk HRD
    public function employerStats(Request $request)
    {
        $user = $request->user();
        $company = $user->company;
        if (!$company) return response()->json(['error' => 'No company'], 404);

        $activeJobs = $company->jobListings()->where('is_active', true)->count();
        $totalApplicants = JobApplication::whereIn('job_listing_id', $company->jobListings()->pluck('id'))->count();

        return response()->json([
            'active_jobs' => $activeJobs,
            'total_applicants' => $totalApplicants,
        ]);
    }

    public function stats(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'employer') {
            $company = $user->company;
            $activeJobs = $company ? $company->jobListings()->where('is_active', true)->count() : 0;
            $totalApplicants = $company ? JobApplication::whereIn('job_listing_id', $company->jobListings()->pluck('id'))->count() : 0;
            $acceptedCount = $company ? JobApplication::whereIn('job_listing_id', $company->jobListings()->pluck('id'))
                ->where('status', 'accepted')->count() : 0;
            return response()->json([
                'active_jobs'       => $activeJobs,
                'total_applicants'  => $totalApplicants,
                'accepted_count'    => $acceptedCount,
                'interview_count'   => $acceptedCount,
            ]);
        } else {
            return response()->json([
                'applied_count' => $user->applications()->count(),
                'saved_count' => $user->savedJobs()->count(),
                'pending_count' => $user->applications()->where('status', 'pending')->count(),
                'reviewed_count' => $user->applications()->where('status', 'reviewed')->count(),
                'accepted_count' => $user->applications()->where('status', 'accepted')->count(),
            ]);
        }
    }
}
