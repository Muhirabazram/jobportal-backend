<?php

namespace App\Http\Controllers\Api;

use App\Models\JobListing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class JobController extends Controller
{
    // Public: list lowongan dengan filter
    public function index(Request $request)
    {
        $query = JobListing::with(['company', 'category', 'jobType'])
            ->where('is_active', true);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhereHas('company', fn($q2) => $q2->where('company_name', 'like', "%{$search}%"));
            });
        }

        // Filter multi kategori
        if ($request->has('categories')) {
            $query->whereHas('category', fn($q) => $q->whereIn('slug', (array) $request->input('categories')));
        }

        // Filter multi tipe
        if ($request->has('job_types')) {
            $query->whereHas('jobType', fn($q) => $q->whereIn('slug', (array) $request->input('job_types')));
        }

        // Lokasi
        if ($request->filled('location')) {
            $query->where('location', 'ilike', "%{$request->location}%");
        }

        $jobs = $query->latest()->paginate(15);
        return response()->json($jobs);
    }

    // Public: detail lowongan
    public function show($slug)
    {
        $job = JobListing::with(['company', 'category', 'jobType'])->where('slug', $slug)->firstOrFail();
        return response()->json($job);
    }

    // Employer: buat lowongan
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'employer' || !$user->company) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'job_type_id' => 'required|exists:job_types,id',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'location' => 'required|string|max:255',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
        ]);

        $validated['company_id'] = $user->company->id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();

        $job = JobListing::create($validated);
        return response()->json($job, 201);
    }

    // Employer: update lowongan
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $job = JobListing::where('id', $id)->where('company_id', $user->company->id)->firstOrFail();

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'job_type_id' => 'sometimes|exists:job_types,id',
            'description' => 'sometimes|string',
            'requirements' => 'nullable|string',
            'location' => 'sometimes|string|max:255',
            'salary_min' => 'nullable|numeric|min:0|max:9999999999',
            'salary_max' => 'nullable|numeric|min:0|max:9999999999',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        }

        $job->update($validated);
        return response()->json($job);
    }

    // Employer: hapus lowongan
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $job = JobListing::where('id', $id)->where('company_id', $user->company->id)->firstOrFail();
        $job->delete();
        return response()->json(['message' => 'Job deleted']);
    }

    // Employer: list lowongan milik sendiri
    public function myJobs(Request $request)
    {
        $company = $request->user()->company;
        if (!$company) return response()->json(['message' => 'No company'], 404);

        $jobs = $company->jobListings()->with(['category', 'jobType', 'applications.user'])->latest()->paginate(10);
        return response()->json($jobs);
    }
}