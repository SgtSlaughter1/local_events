<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Event $event)
    {
        return $user->id === $event->created_by;
    }

    public function delete(User $user, Event $event)
    {
        return $user->id === $event->created_by;
    }
}
