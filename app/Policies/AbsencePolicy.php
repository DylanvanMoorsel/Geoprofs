<?php

namespace App\Policies;

use App\Models\Absence;
use App\Models\User;

class AbsencePolicy
{
    /** Een medewerker mag alleen een eigen, nog openstaande aanvraag intrekken. */
    public function cancel(User $user, Absence $absence): bool
    {
        return $absence->user_id === $user->id && $absence->isPending();
    }

    /** Alleen de manager van de medewerker mag een aanvraag beoordelen. */
    public function review(User $user, Absence $absence): bool
    {
        return $user->isManager() && $absence->user->manager_id === $user->id;
    }
}