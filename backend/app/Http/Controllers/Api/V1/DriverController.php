<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDriverRequest;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::all();
        return DriverResource::collection($drivers);
    }

    public function store(CreateDriverRequest $request)
    {
        $driver = Driver::create($request->validated());
        return new DriverResource($driver);
    }

    public function show($id)
    {
        $driver = Driver::findOrFail($id);
        return new DriverResource($driver);
    }

    public function update(CreateDriverRequest $request, $id)
    {
        $driver = Driver::findOrFail($id);
        $driver->update($request->validated());
        return new DriverResource($driver);
    }

    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();
        return response()->noContent();
    }
}