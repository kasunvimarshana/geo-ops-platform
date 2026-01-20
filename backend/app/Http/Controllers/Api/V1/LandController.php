<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLandRequest;
use App\Http\Requests\UpdateLandRequest;
use App\Http\Resources\LandResource;
use App\Models\Land;
use App\Repositories\LandRepository;
use Illuminate\Http\Request;

class LandController extends Controller
{
    protected $landRepository;

    public function __construct(LandRepository $landRepository)
    {
        $this->landRepository = $landRepository;
    }

    public function index(Request $request)
    {
        $lands = $this->landRepository->getAllLands($request->user()->organization_id);
        return LandResource::collection($lands);
    }

    public function store(CreateLandRequest $request)
    {
        $land = $this->landRepository->createLand($request->validated());
        return new LandResource($land);
    }

    public function show($id)
    {
        $land = $this->landRepository->findLandById($id);
        return new LandResource($land);
    }

    public function update(UpdateLandRequest $request, $id)
    {
        $land = $this->landRepository->updateLand($id, $request->validated());
        return new LandResource($land);
    }

    public function destroy($id)
    {
        $this->landRepository->deleteLand($id);
        return response()->json(['message' => 'Land deleted successfully.'], 204);
    }
}