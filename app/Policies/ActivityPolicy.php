<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the activity.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Activity  $activity
     * @return bool
     */
    public function update(User $user, Activity $activity)
    {
        return $user->user_id === $activity->creator_id;
    }

    /**
     * Determine whether the user can delete the activity.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Activity  $activity
     * @return bool
     */
    public function delete(User $user, Activity $activity)
    {
        return $user->user_id === $activity->creator_id;
    }
} 