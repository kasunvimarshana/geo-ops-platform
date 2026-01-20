<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Field::with(['user', 'organization'])
            ->where('organization_id', $user->organization_id);

        // Filter by crop type
        if ($request->has('crop_type')) {
            $query->where('crop_type', $request->crop_type);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $fields = $query->paginate($request->get('per_page', 15));

        return response()->json($fields);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'boundary' => 'required|array|min:3',
            'boundary.*.latitude' => 'required|numeric|between:-90,90',
            'boundary.*.longitude' => 'required|numeric|between:-180,180',
            'area' => 'required|numeric|min:0',
            'perimeter' => 'required|numeric|min:0',
            'crop_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'measurement_type' => 'required|in:walk_around,polygon,manual',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();

        $field = Field::create([
            'name' => $request->name,
            'organization_id' => $user->organization_id,
            'user_id' => $user->id,
            'boundary' => $request->boundary,
            'area' => $request->area,
            'perimeter' => $request->perimeter,
            'crop_type' => $request->crop_type,
            'notes' => $request->notes,
            'measurement_type' => $request->measurement_type,
            'measured_at' => now(),
        ]);

        return response()->json([
            'message' => 'Field created successfully',
            'field' => $field->load(['user', 'organization'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        $field = Field::with(['user', 'organization', 'jobs'])
            ->where('organization_id', $user->organization_id)
            ->findOrFail($id);

        return response()->json($field);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $field = Field::where('organization_id', $user->organization_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'boundary' => 'sometimes|required|array|min:3',
            'boundary.*.latitude' => 'required_with:boundary|numeric|between:-90,90',
            'boundary.*.longitude' => 'required_with:boundary|numeric|between:-180,180',
            'area' => 'sometimes|required|numeric|min:0',
            'perimeter' => 'sometimes|required|numeric|min:0',
            'crop_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $field->update($request->only([
            'name', 'boundary', 'area', 'perimeter', 'crop_type', 'notes'
        ]));

        return response()->json([
            'message' => 'Field updated successfully',
            'field' => $field->load(['user', 'organization'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        $field = Field::where('organization_id', $user->organization_id)
            ->findOrFail($id);

        $field->delete();

        return response()->json(['message' => 'Field deleted successfully']);
    }

    /**
     * Generate report for a field
     */
    public function report(Request $request, string $id)
    {
        $user = auth()->user();
        $field = Field::where('organization_id', $user->organization_id)
            ->findOrFail($id);

        $reportService = new ReportService();
        $reportData = $reportService->generateFieldReport($field);

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
