<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\JobTypeController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\SavedJobController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\HomeController;

// Public routes
Route::post('/register/job-seeker', [AuthController::class, 'registerJobSeeker']);
Route::post('/register/employer', [AuthController::class, 'registerEmployer']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/check-email', [AuthController::class, 'checkEmail']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/job-types', [JobTypeController::class, 'index']);
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{slug}', [JobController::class, 'show']);
Route::get('/home-stats', [HomeController::class, 'stats']);

// Protected routes (require Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::delete('/applications/{id}', [ApplicationController::class, 'destroy']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/profile/avatar', [AuthController::class, 'updateAvatar']);
    Route::delete('/profile/avatar', [AuthController::class, 'deleteAvatar']);
    Route::post('/profile/cv', [AuthController::class, 'updateCv']);
    Route::delete('/profile/cv', [AuthController::class, 'deleteCv']);
    Route::delete('/profile/delete-account', [AuthController::class, 'deleteAccount']);
    
    // Dashboard stats based on role
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Saved jobs (job seeker only)
    Route::middleware('role:job_seeker')->group(function () {
        Route::get('/saved-jobs', [SavedJobController::class, 'index']);
        Route::post('/jobs/{slug}/save', [SavedJobController::class, 'store']);
        Route::delete('/jobs/{slug}/unsave', [SavedJobController::class, 'destroy']);
        Route::post('/jobs/{slug}/apply', [ApplicationController::class, 'store']);
        Route::get('/my-applications', [ApplicationController::class, 'myApplications']);
    });

    // Employer routes
    Route::middleware('role:employer')->group(function () {
        Route::post('/jobs', [JobController::class, 'store']);
        Route::put('/jobs/{id}', [JobController::class, 'update']);
        Route::delete('/jobs/{id}', [JobController::class, 'destroy']);
        Route::get('/my-jobs', [JobController::class, 'myJobs']); // lowongan milik perusahaan
        Route::get('/jobs/{id}/applications', [ApplicationController::class, 'indexForEmployer']);
        Route::put('/applications/{id}/status', [ApplicationController::class, 'updateStatus']);
        Route::get('/my-interviews', [ApplicationController::class, 'myInterviews']);
    });
});