<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMachineRequest;
use App\Http\Resources\MachineResource;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MachineController extends Controller
{
    public function index()
    {
        $machines = Machine::all();
        return MachineResource::collection($machines);
    }

    public function store(CreateMachineRequest $request)
    {
        $machine = Machine::create($request->validated());
        return new MachineResource($machine);
    }

    public function show($id)
    {
        $machine = Machine::findOrFail($id);
        return new MachineResource($machine);
    }

    public function update(CreateMachineRequest $request, $id)
    {
        $machine = Machine::findOrFail($id);
        $machine->update($request->validated());
        return new MachineResource($machine);
    }

    public function destroy($id)
    {
        $machine = Machine::findOrFail($id);
        $machine->delete();
        return response()->noContent();
    }
}