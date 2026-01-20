<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    /**
     * Display a listing of jobs for the authenticated user's organization.
     */
    public function index(Request $request)
    {
        $query = Job::with(['organization', 'field', 'creator', 'assignee'])
            ->where('organization_id', $request->user()->organization_id);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by assignee
        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by field
        if ($request->has('field_id')) {
            $query->where('field_id', $request->field_id);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page', 15);
        $jobs = $query->paginate($perPage);

        return response()->json($jobs);
    }

    /**
     * Store a newly created job in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'field_id' => 'nullable|exists:fields,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
            'location' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $job = Job::create([
            'title' => $request->title,
            'description' => $request->description,
            'organization_id' => $request->user()->organization_id,
            'field_id' => $request->field_id,
            'created_by' => $request->user()->id,
            'assigned_to' => $request->assigned_to,
            'status' => $request->status ?? 'pending',
            'priority' => $request->priority ?? 'medium',
            'due_date' => $request->due_date,
            'location' => $request->location,
        ]);

        $job->load(['organization', 'field', 'creator', 'assignee']);

        return response()->json([
            'message' => 'Job created successfully',
            'job' => $job
        ], 201);
    }

    /**
     * Display the specified job.
     */
    public function show(Request $request, $id)
    {
        $job = Job::with(['organization', 'field', 'creator', 'assignee', 'invoices'])
            ->where('organization_id', $request->user()->organization_id)
            ->findOrFail($id);

        return response()->json($job);
    }

    /**
     * Update the specified job in storage.
     */
    public function update(Request $request, $id)
    {
        $job = Job::where('organization_id', $request->user()->organization_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'field_id' => 'nullable|exists:fields,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
            'location' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only([
            'title', 'description', 'field_id', 'assigned_to',
            'status', 'priority', 'due_date', 'location'
        ]);

        // Automatically set timestamps based on status changes
        if ($request->has('status')) {
            if ($request->status === 'in_progress' && $job->status !== 'in_progress') {
                $updateData['started_at'] = now();
            } elseif ($request->status === 'completed' && $job->status !== 'completed') {
                $updateData['completed_at'] = now();
            }
        }

        $job->update($updateData);
        $job->load(['organization', 'field', 'creator', 'assignee']);

        return response()->json([
            'message' => 'Job updated successfully',
            'job' => $job
        ]);
    }

    /**
     * Remove the specified job from storage.
     */
    public function destroy(Request $request, $id)
    {
        $job = Job::where('organization_id', $request->user()->organization_id)
            ->findOrFail($id);

        $job->delete();

        return response()->json([
            'message' => 'Job deleted successfully'
        ]);
    }

    /**
     * Generate report for a job
     */
    public function report(Request $request, $id)
    {
        $job = Job::where('organization_id', $request->user()->organization_id)
            ->findOrFail($id);

        $reportService = new ReportService();
        $reportData = $reportService->generateJobReport($job);

        $format = $request->get('format', 'json');

        switch ($format) {
            case 'html':
                $content = $reportService->formatAsHtml($reportData);
                return response($content)->header('Content-Type', 'text/html');
            
            case 'json':
            default:
                return response()->json($reportData);
        }
    }
}
