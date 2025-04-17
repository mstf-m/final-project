<?php

namespace App\Http\Controllers\API;

use App\Models\Activity;
use App\Models\Participant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['creator', 'category'])
            ->orderBy('start_time', 'asc')
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
        ]);

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
        ]);

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
}