<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobType;

class JobTypeController extends Controller
{
    public function index()
    {
        return response()->json(JobType::all());
    }
}