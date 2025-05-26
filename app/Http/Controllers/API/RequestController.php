<?php

namespace App\Http\Controllers\API;

use App\Models\Activity;
use App\Models\ActivityRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Participant;

class RequestController extends Controller
{
    public function store(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $joinRequest = ActivityRequest::create([
            'activity_id' => $activity->activity_id,
            'user_id' => $request->user()->user_id,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        // Notify activity creator
        Notification::create([
            'user_id' => $activity->creator_id,
            'message' => 'New join request for your activity: ' . $activity->title
        ]);

        return response()->json($joinRequest, 201);
    }

    public function updateStatus(Request $request, ActivityRequest $activityRequest)
    {
        $this->authorize('update', $activityRequest->activity);

        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,canceled,expired'
        ]);

        $activityRequest->update(['status' => $validated['status']]);

        if ($validated['status'] === 'accepted') {
            Participant::create([
                'activity_id' => $activityRequest->activity_id,
                'user_id' => $activityRequest->user_id,
                'status' => 'active'
            ]);

            Notification::create([
                'user_id' => $activityRequest->user_id,
                'message' => "Your request to join '{$activityRequest->activity->title}' was accepted!"
            ]);
        }

        return response()->json($activityRequest);
    }

    public function myRequests(Request $request)
    {
        $requests = ActivityRequest::with(['activity'])
            ->where('user_id', $request->user()->user_id)
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        return response()->json($requests);
    }
}