<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::all();
        return OrganizationResource::collection($organizations);
    }

    public function store(CreateOrganizationRequest $request)
    {
        $organization = Organization::create($request->validated());
        return new OrganizationResource($organization, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $organization = Organization::findOrFail($id);
        return new OrganizationResource($organization);
    }

    public function update(CreateOrganizationRequest $request, $id)
    {
        $organization = Organization::findOrFail($id);
        $organization->update($request->validated());
        return new OrganizationResource($organization);
    }

    public function destroy($id)
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();
        return response()->noContent();
    }
}