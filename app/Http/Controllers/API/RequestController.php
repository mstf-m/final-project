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
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
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
        try {
            // For other status updates, check if user is the activity creator
            $this->authorize('update', $activityRequest->activity);

            $validated = $request->validate([
                'status' => 'required|in:accepted,rejected'
            ]);

            $activityRequest->update(['status' => $validated['status']]);

            if ($validated['status'] === 'accepted') {
                // Check if user is already a participant
                $existingParticipant = Participant::where('activity_id', $activityRequest->activity_id)
                    ->where('user_id', $activityRequest->user_id)
                    ->first();

                if (!$existingParticipant) {
                    Participant::create([
                        'activity_id' => $activityRequest->activity_id,
                        'user_id' => $activityRequest->user_id,
                        'status' => 'accepted'
                    ]);
                }

                Notification::create([
                    'user_id' => $activityRequest->user_id,
                    'message' => "Your request to join '{$activityRequest->activity->title}' was accepted!"
                ]);
            } else {
                Notification::create([
                    'user_id' => $activityRequest->user_id,
                    'message' => "Your request to join '{$activityRequest->activity->title}' was rejected."
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => $activityRequest->fresh(['user', 'activity'])
            ]);
        } catch (\Exception $e) {
            \Log::error('Request status update error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update request status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function myRequests(Request $request)
    {
        $requests = ActivityRequest::with(['activity'])
            ->where('user_id', $request->user()->user_id)
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return response()->json($requests);
    }
}