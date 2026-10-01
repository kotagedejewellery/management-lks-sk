<?php

namespace App\Policies;

use App\Models\LksPeriod;
use App\Models\User;

class LksPeriodPolicy
{
    public function activate(User $user, LksPeriod $period): bool
    {
        return $user->isAdmin();
    }

    public function close(User $user, LksPeriod $period): bool
    {
        return $user->isAdmin();
    }
}
