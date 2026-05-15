<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobListing;
use App\Models\SavedJob;

class SavedJobController extends Controller
{
    // Simpan lowongan
    public function store(Request $request, $jobSlug)
    {
        $user = $request->user();
        $job = JobListing::where('slug', $jobSlug)->firstOrFail();

        $saved = SavedJob::firstOrCreate([
            'user_id' => $user->id,
            'job_listing_id' => $job->id,
        ]);

        return response()->json($saved);
    }

    // Hapus dari simpanan
    public function destroy(Request $request, $jobSlug)
    {
        $user = $request->user();
        $job = JobListing::where('slug', $jobSlug)->firstOrFail();

        SavedJob::where('user_id', $user->id)
            ->where('job_listing_id', $job->id)
            ->delete();

        return response()->json(['message' => 'Removed from saved']);
    }

    // List lowongan yang disimpan user
    public function index(Request $request)
    {
        $user = $request->user();
        $saved = $user->savedJobs()->with('jobListing.company')->paginate(10);
        return response()->json($saved);
    }
}
