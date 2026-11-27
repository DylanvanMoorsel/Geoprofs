<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Services\OccupancyService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ManagerDashboardController extends Controller
{
    public function show(Request $request, OccupancyService $occupancy): array
    {
        $manager = $request->user();
        $department = $manager->department;

        $today = CarbonImmutable::now()->startOfDay();
        $nextWorkday = $today->addWeekdays(1);

        $todayData = $department ? $occupancy->forDay($department, $today) : null;
        $tomorrowData = $department ? $occupancy->forDay($department, $nextWorkday) : null;

        $pending = Absence::pending()
            ->whereHas('user', fn ($query) => $query->where('manager_id', $manager->id));

        return [
            'pending_requests' => (clone $pending)->count(),
            'minimum_coverage' => config('occupancy.threshold', 75),
            'today' => $todayData ? Arr::except($todayData, 'absent') : null,
            'tomorrow' => $tomorrowData ? Arr::except($tomorrowData, 'absent') : null,
            'absent_today' => $todayData['absent'] ?? [],
            'priority_requests' => $pending
                ->with('user')
                ->orderBy('start_date')
                ->orderBy('id')
                ->limit(5)
                ->get()
                ->map->summaryWithEmployee()
                ->all(),
        ];
    }
}