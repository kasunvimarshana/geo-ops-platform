<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::where('organization_id', Auth::user()->organization_id)->get();
        return SubscriptionResource::collection($subscriptions);
    }

    public function store(CreateSubscriptionRequest $request)
    {
        $subscription = Subscription::create([
            'organization_id' => Auth::user()->organization_id,
            'tier' => $request->tier,
            'start_date' => now(),
            'end_date' => now()->addMonth($request->duration),
            'status' => 'active',
        ]);

        return new SubscriptionResource($subscription);
    }

    public function show($id)
    {
        $subscription = Subscription::findOrFail($id);
        return new SubscriptionResource($subscription);
    }

    public function update(CreateSubscriptionRequest $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->update($request->validated());

        return new SubscriptionResource($subscription);
    }

    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return response()->json(['message' => 'Subscription deleted successfully.']);
    }
}