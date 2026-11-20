<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Services\AbsenceService;
use Illuminate\Http\Request;

class EmployeeDashboardController extends Controller
{
    public function show(Request $request, AbsenceService $service): array
    {
        $user = $request->user()->load('department');
        $year = now()->year;
        $perDay = (float) config('occupancy.hours_per_day', 8);

        $approvedThisYear = $user->absences()
            ->approved()
            ->overlapping("$year-01-01", "$year-12-31")
            ->get();

        $daysTaken = fn (array $types) => $approvedThisYear
            ->whereIn('type', $types)
            ->sum(fn (Absence $absence) => $absence->workdaysInYear($year));

        $recent = $user->absences()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return [
            'user' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'department' => $user->department?->name,
            ],
            'stats' => [
                'vacation_hours' => $user->vacation_hours,
                'vacation_days' => round($user->vacation_hours / $perDay, 2),
                'reserved_vacation_hours' => $user->vacation_hours - $service->availableVacationHours($user),
                'vacation_days_taken_this_year' => $daysTaken([Absence::TYPE_VACATION]),
                'sick_days_this_year' => $daysTaken([Absence::TYPE_SICK]),
                'other_days_this_year' => $daysTaken([Absence::TYPE_APPOINTMENT, Absence::TYPE_OTHER]),
            ],
            'pending_requests' => $user->absences()->pending()->count(),
            'recent_requests' => $recent->map->summary()->all(),
        ];
    }
}