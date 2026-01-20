<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Http\Resources\LandResource;
use App\Services\JobService;
use App\Services\LandMeasurementService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $jobService;
    protected $landMeasurementService;

    public function __construct(JobService $jobService, LandMeasurementService $landMeasurementService)
    {
        $this->jobService = $jobService;
        $this->landMeasurementService = $landMeasurementService;
    }

    public function index(Request $request)
    {
        $totalJobs = $this->jobService->getTotalJobs();
        $completedJobs = $this->jobService->getCompletedJobs();
        $totalLandsMeasured = $this->landMeasurementService->getTotalLandsMeasured();

        return response()->json([
            'total_jobs' => $totalJobs,
            'completed_jobs' => $completedJobs,
            'total_lands_measured' => $totalLandsMeasured,
        ]);
    }

    public function getRecentJobs(Request $request)
    {
        $recentJobs = $this->jobService->getRecentJobs();

        return JobResource::collection($recentJobs);
    }

    public function getRecentLands(Request $request)
    {
        $recentLands = $this->landMeasurementService->getRecentLands();

        return LandResource::collection($recentLands);
    }
}