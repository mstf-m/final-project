<?php

namespace App\Http\Controllers\API;

use App\Models\Activity;
use App\Models\Participant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['creator', 'category']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $activities = $query->orderBy('start_time', 'asc')
            ->paginate(10);

        return response()->json($activities);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'max_participants' => 'nullable|integer|min:1',
            'category_id' => 'nullable|exists:categories,category_id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            if ($file->isValid()) {
                try {
                    // Ensure the directory exists
                    $uploadPath = public_path('storage/activity-images');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    // Move the file to the public directory
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move($uploadPath, $filename);
                    $validated['image_url'] = '/storage/activity-images/' . $filename;
                } catch (\Exception $e) {
                    \Log::error('File upload error: ' . $e->getMessage());
                    return response()->json(['message' => 'Error uploading file: ' . $e->getMessage()], 500);
                }
            }
        }

        $activity = $request->user()->activities()->create($validated);

        return response()->json($activity, 201);
    }

    public function show(Activity $activity)
    {
        $activity->load(['creator', 'category', 'participants.user', 'requests.user']);
        return response()->json($activity);
    }

    public function update(Request $request, Activity $activity)
    {
        $this->authorize('update', $activity);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:100',
            'description' => 'sometimes|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'max_participants' => 'sometimes|integer|min:1',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($activity->image_url) {
                $oldPath = str_replace('/storage/', '', $activity->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('image')->store('activity-images', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        $activity->update($validated);

        return response()->json($activity);
    }

    public function destroy(Activity $activity)
    {
        $this->authorize('delete', $activity);
        $activity->delete();
        return response()->json(null, 204);
    }

    public function participants(Activity $activity)
    {
        $participants = $activity->participants()->with('user')->paginate(10);
        return response()->json($participants);
    }

    public function userActivities(Request $request, $userId)
    {
        $activities = Activity::with(['creator', 'category'])
            ->where('creator_id', $userId)
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        return response()->json($activities);
    }

    public function myActivitiesWithRequests(Request $request)
    {
        $activities = Activity::with([
            'creator',
            'category',
            'requests.user',
            'participants.user'
        ])
            ->where('creator_id', $request->user()->user_id)
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        return response()->json($activities);
    }
}