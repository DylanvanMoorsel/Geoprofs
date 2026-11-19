<?php

namespace App\Services;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** De regels rond verlof aanvragen, beoordelen en intrekken. */
class AbsenceService
{
    /** Saldo min de uren die al in openstaande vakantieaanvragen zijn gereserveerd. */
    public function availableVacationHours(User $user): float
    {
        $reserved = $user->absences()
            ->pending()
            ->where('type', Absence::TYPE_VACATION)
            ->get()
            ->sum(fn (Absence $absence) => $absence->hours());

        return (float) $user->vacation_hours - $reserved;
    }

    /** Ziekmeldingen zijn direct goedgekeurd, al het andere moet beoordeeld worden. */
    public function request(User $user, array $data): Absence
    {
        $sick = $data['type'] === Absence::TYPE_SICK;

        return Absence::create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason'] ?? null,
            'status' => $sick ? Absence::STATUS_APPROVED : Absence::STATUS_PENDING,
        ]);
    }

    /** Keurt goed en boekt bij vakantie de uren af, in één transactie. */
    public function approve(Absence $absence, User $reviewer): Absence
    {
        return DB::transaction(function () use ($absence, $reviewer) {
            $absence = $this->lockPending($absence);

            if ($absence->type === Absence::TYPE_VACATION) {
                $employee = User::whereKey($absence->user_id)->lockForUpdate()->firstOrFail();
                $hours = $absence->hours();

                if ((float) $employee->vacation_hours < $hours) {
                    throw ValidationException::withMessages([
                        'absence' => 'De medewerker heeft niet genoeg verlofuren voor deze aanvraag.',
                    ]);
                }

                $employee->decrement('vacation_hours', $hours);
            }

            return $this->markReviewed($absence, $reviewer, Absence::STATUS_APPROVED);
        });
    }

    public function reject(Absence $absence, User $reviewer): Absence
    {
        return DB::transaction(function () use ($absence, $reviewer) {
            return $this->markReviewed($this->lockPending($absence), $reviewer, Absence::STATUS_REJECTED);
        });
    }

    public function cancel(Absence $absence): void
    {
        $absence->delete();
    }

    private function lockPending(Absence $absence): Absence
    {
        $fresh = Absence::whereKey($absence->id)->lockForUpdate()->firstOrFail();

        if (! $fresh->isPending()) {
            throw ValidationException::withMessages(['absence' => 'Deze aanvraag is al beoordeeld.']);
        }

        return $fresh;
    }

    private function markReviewed(Absence $absence, User $reviewer, string $status): Absence
    {
        $absence->update([
            'status' => $status,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);

        return $absence;
    }
}