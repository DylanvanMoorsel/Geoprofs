<?php

namespace App\Services;

use App\Models\Absence;
use App\Models\Department;
use Carbon\CarbonImmutable;

class OccupancyService
{
    public function forWeek(Department $department, CarbonImmutable $date): array
    {
        $monday = $date->startOfWeek();
        $friday = $monday->addDays(4);
        $threshold = config('occupancy.threshold', 75);

        $employees = $department->users()->orderBy('first_name')->get();

        $absences = Absence::approved()
            ->overlapping($monday->toDateString(), $friday->toDateString())
            ->whereIn('user_id', $employees->pluck('id'))
            ->get()
            ->groupBy('user_id');

        $dates = collect(range(0, 4))->map(fn ($i) => $monday->addDays($i)->toDateString());

        $rows = $employees->map(function ($user) use ($dates, $absences) {
            $days = [];
            foreach ($dates as $day) {
                $status = 'aanwezig';
                foreach ($absences->get($user->id, collect()) as $absence) {
                    if ($absence->start_date->toDateString() <= $day
                        && $absence->end_date->toDateString() >= $day) {
                        $status = $absence->type;
                        break;
                    }
                }
                $days[$day] = $status;
            }

            return ['id' => $user->id, 'name' => $user->name, 'days' => $days];
        });

        $total = $employees->count();
        $summary = $dates->map(function ($day) use ($rows, $total, $threshold) {
            $absent = $rows->filter(fn ($r) => $r['days'][$day] !== 'aanwezig')->count();
            $present = $total - $absent;
            $raw = $total > 0 ? $present / $total * 100 : null;

            return [
                'date' => $day,
                'present' => $present,
                'total' => $total,
                'percentage' => $raw === null ? null : (int) round($raw),
                'below_threshold' => $raw !== null && $raw < $threshold,
            ];
        })->values();

        return [
            'week' => $monday->isoFormat('GGGG-[W]WW'),
            'days' => $summary->all(),
            'employees' => $rows->values()->all(),
        ];
    }
}