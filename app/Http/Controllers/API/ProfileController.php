<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\ActivityRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Get authenticated user's profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        // Return the authenticated user's data
        return response()->json([
            'status' => 'success',
            'data' => new UserResource($request->user())
        ]);
    }

    /**
     * Upload user's profile picture
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $user = $request->user();
            
            // Delete old avatar if exists
            if ($user->avatar_url) {
                $oldPath = str_replace('/storage/', '', $user->avatar_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Get the file
            $file = $request->file('avatar');
            if (!$file || !$file->isValid()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid file upload'
                ], 400);
            }

            // Generate a unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::random(10) . '.' . $extension;
            
            // Move the file to storage
            $file->move(storage_path('app/public/avatars'), $filename);
            
            // Update user's avatar_url
            $user->update([
                'avatar_url' => '/storage/avatars/' . $filename
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile picture uploaded successfully',
                'data' => new UserResource($user->fresh())
            ]);

        } catch (\Exception $e) {
            \Log::error('Avatar upload error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload avatar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user's profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'firstname' => 'sometimes|string|max:50',
            'lastname' => 'sometimes|string|max:50',
            'gender' => 'sometimes|in:male,female',
            'birthday' => 'sometimes|date',
            'bio' => 'sometimes|string|max:255',
        ]);

        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user->fresh())
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();
        $now = Carbon::now();

        // Get activities where the user was a participant
        $activities = Activity::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        })
        ->where('end_date', '>=', $now)
        ->with(['category', 'creator', 'participants'])
        ->orderBy('start_date', 'asc')
        ->get();

        // Get requests for upcoming activities
        $requests = ActivityRequest::where('user_id', $user->user_id)
            ->whereHas('activity', function ($query) use ($now) {
                $query->where('end_date', '>=', $now);
            })
            ->with(['activity.category', 'activity.creator', 'activity.participants'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'activities' => $activities,
            'requests' => $requests
        ]);
    }
}