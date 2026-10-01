<?php

namespace App\Policies;

use App\Models\PeriodParticipantSnapshot;
use App\Models\User;

class PeriodParticipantSnapshotPolicy
{
    public function view(User $user, PeriodParticipantSnapshot $participant): bool
    {
        return $user->isAdmin()
            || $user->is($participant->user)
            || $participant->leader_user_id_snapshot === $user->getKey();
    }

    public function record(User $user, PeriodParticipantSnapshot $participant): bool
    {
        return $user->isAdmin() || $user->getKey() === $participant->user_id;
    }
}
