<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Meet;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetEditPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    public function userOwnedMeeting(User $user, Meet $meet) {
        return $user->id === $meet->user_id;
    }
}
