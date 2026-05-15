<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobListing;
use App\Models\JobApplication;

class ApplicationController extends Controller
{
    // Job seeker apply
    public function store(Request $request, $slug)
    {
        $user = $request->user();
        if ($user->role !== 'job_seeker') {
            return response()->json(['message' => 'Only job seekers can apply'], 403);
        }

        $job = JobListing::where('slug', $slug)->firstOrFail();

        // Cek apakah sudah pernah melamar
        $existing = JobApplication::where('job_listing_id', $job->id)
            ->where('user_id', $user->id)
            ->first();
        if ($existing) {
            return response()->json(['message' => 'Anda sudah melamar lowongan ini'], 422);
        }

        // Validasi (optional)
        $request->validate([
            'experience' => 'nullable|string',
            'cover_letter' => 'nullable|string',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Upload resume jika ada
        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
        }

        $application = JobApplication::create([
            'job_listing_id' => $job->id,
            'user_id' => $user->id,
            'experience' => $request->input('experience'),
            'cover_letter' => $request->input('cover_letter'),
            'resume' => $resumePath,
        ]);

        return response()->json($application, 201);
    }

    // Employer: lihat pelamar per lowongan
    public function indexForEmployer(Request $request, $jobId)
    {
        $user = $request->user();
        if ($user->role !== 'employer') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $job = JobListing::where('id', $jobId)
            ->where('company_id', $user->company->id)
            ->firstOrFail();

        $applications = $job->applications()->with('user')->paginate(10);
        return response()->json($applications);
    }

    // Job seeker: lihat lamaran sendiri
    public function myApplications(Request $request)
    {
        $applications = $request->user()->applications()
            ->with(['jobListing.company', 'jobListing.jobType'])
            ->latest()
            ->paginate(10);
        return response()->json($applications);
    }

    // Employer: update status lamaran
    public function updateStatus(Request $request, $id)
    {
        $application = JobApplication::findOrFail($id);
        $employerCompanyId = $request->user()->company->id;

        if ($application->jobListing->company_id !== $employerCompanyId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,reviewed,accepted,rejected,hired',
            'rejection_reason' => 'nullable|string'
        ]);

        $updateData = ['status' => $request->status];
        if ($request->has('rejection_reason')) {
            $updateData['rejection_reason'] = $request->rejection_reason;
        }

        $application->update($updateData);

        return response()->json($application);
    }

    public function myInterviews(Request $request)
    {
        $company = $request->user()->company;
        if (!$company) return response()->json(['message' => 'No company'], 404);

        $interviews = JobApplication::whereIn('job_listing_id', $company->jobListings()->pluck('id'))
            ->where('status', 'accepted')
            ->with(['user', 'jobListing'])
            ->latest()
            ->get();

        return response()->json($interviews);
    }

    public function destroy(Request $request, $id)
    {
        $application = JobApplication::findOrFail($id);

        // Hanya pemilik lamaran yang boleh menghapus
        if ($application->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $application->delete();

        return response()->json(['message' => 'Lamaran berhasil ditarik']);
    }
}