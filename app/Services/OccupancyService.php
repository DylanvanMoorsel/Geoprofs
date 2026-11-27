<?php

namespace App\Services;

use App\Models\Absence;
use App\Models\Department;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Berekent de bezetting van een afdeling. Aanwezigheid wordt niet opgeslagen:
 * wie op een dag geen goedgekeurde afwezigheid heeft, is aanwezig.
 */
class OccupancyService
{
    /** Bezetting van maandag t/m vrijdag van de week waarin $date valt. */
    public function forWeek(Department $department, CarbonImmutable $date): array
    {
        $monday = $date->startOfWeek();
        $days = collect(range(0, 4))->map(fn (int $i) => $monday->addDays($i));

        $employees = $department->employees()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $absences = $this->approvedAbsences($employees, $days->first(), $days->last());

        $rows = $employees->map(function ($user) use ($days, $absences) {
            $statuses = [];
            foreach ($days as $day) {
                $statuses[$day->toDateString()] = $this->absenceOn($absences->get($user->id, collect()), $day)?->type
                    ?? 'aanwezig';
            }

            return ['id' => $user->id, 'name' => $user->name, 'days' => $statuses];
        });

        $summary = $days->map(function (CarbonImmutable $day) use ($rows) {
            $absent = $rows->filter(fn ($row) => $row['days'][$day->toDateString()] !== 'aanwezig')->count();

            return $this->summarize($day, $rows->count(), $absent);
        });

        return [
            'week' => $monday->isoFormat('GGGG-[W]WW'),
            'days' => $summary->values()->all(),
            'employees' => $rows->values()->all(),
        ];
    }

    /** Bezetting van één dag, inclusief wie er afwezig is. */
    public function forDay(Department $department, CarbonImmutable $day): array
    {
        $employees = $department->employees()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $absences = $this->approvedAbsences($employees, $day, $day);

        $absent = $employees
            ->map(fn ($user) => [$user, $this->absenceOn($absences->get($user->id, collect()), $day)])
            ->filter(fn ($pair) => $pair[1] !== null)
            ->map(fn ($pair) => [
                'id' => $pair[0]->id,
                'name' => $pair[0]->name,
                'type' => $pair[1]->type,
                'start_date' => $pair[1]->start_date->toDateString(),
                'end_date' => $pair[1]->end_date->toDateString(),
            ])
            ->values();

        return $this->summarize($day, $employees->count(), $absent->count()) + [
            'absent' => $absent->all(),
        ];
    }

    private function summarize(CarbonImmutable $day, int $total, int $absent): array
    {
        $present = $total - $absent;
        $raw = $total > 0 ? $present / $total * 100 : null;

        return [
            'date' => $day->toDateString(),
            'present' => $present,
            'total' => $total,
            'percentage' => $raw === null ? null : (int) round($raw),
            'below_threshold' => $raw !== null && $raw < config('occupancy.threshold', 75),
        ];
    }

    /** Eén query voor alle goedgekeurde afwezigheden, per medewerker gegroepeerd. */
    private function approvedAbsences(Collection $employees, CarbonImmutable $from, CarbonImmutable $to): Collection
    {
        if ($employees->isEmpty()) {
            return collect();
        }

        return Absence::approved()
            ->overlapping($from->toDateString(), $to->toDateString())
            ->whereIn('user_id', $employees->pluck('id'))
            ->get()
            ->groupBy('user_id');
    }

    private function absenceOn(Collection $userAbsences, CarbonImmutable $day): ?Absence
    {
        $date = $day->toDateString();

        return $userAbsences->first(
            fn (Absence $absence) => $absence->start_date->toDateString() <= $date
                && $absence->end_date->toDateString() >= $date
        );
    }
}
  