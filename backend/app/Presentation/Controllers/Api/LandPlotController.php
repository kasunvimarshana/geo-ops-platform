<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api;

use App\Application\Services\LandPlotService;
use App\Http\Controllers\Controller;
use App\Presentation\Resources\LandPlotResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class LandPlotController extends Controller
{
    public function __construct(
        private readonly LandPlotService $landPlotService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $organizationId = auth()->user()->organization_id;
        $landPlots = $this->landPlotService->getAllByOrganization($organizationId, $request->get('per_page', 15));
        return LandPlotResource::collection($landPlots);
    }

    public function show(int $id): JsonResponse
    {
        $landPlot = $this->landPlotService->getById($id);
        
        if (!$landPlot || $landPlot->organization_id !== auth()->user()->organization_id) {
            return response()->json(['error' => 'Land plot not found'], 404);
        }

        return response()->json(['data' => new LandPlotResource($landPlot)]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'coordinates' => 'required|array|min:3',
            'coordinates.*.latitude' => 'required|numeric',
            'coordinates.*.longitude' => 'required|numeric',
            'measurement_method' => 'required|in:walk_around,manual_points',
            'measured_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['organization_id'] = auth()->user()->organization_id;
        $data['user_id'] = auth()->id();

        $calculations = $this->landPlotService->calculateArea($data['coordinates']);
        $center = $this->landPlotService->calculateCenter($data['coordinates']);

        $data = array_merge($data, $calculations, $center);

        $landPlot = $this->landPlotService->create($data);
        return response()->json(['data' => new LandPlotResource($landPlot)], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $landPlot = $this->landPlotService->getById($id);
        
        if (!$landPlot || $landPlot->organization_id !== auth()->user()->organization_id) {
            return response()->json(['error' => 'Land plot not found'], 404);
        }

        $updated = $this->landPlotService->update($id, $request->all());
        return response()->json(['data' => new LandPlotResource($updated)]);
    }

    public function destroy(int $id): JsonResponse
    {
        $landPlot = $this->landPlotService->getById($id);
        
        if (!$landPlot || $landPlot->organization_id !== auth()->user()->organization_id) {
            return response()->json(['error' => 'Land plot not found'], 404);
        }

        $this->landPlotService->delete($id);
        return response()->json(['message' => 'Land plot deleted successfully']);
    }
}
