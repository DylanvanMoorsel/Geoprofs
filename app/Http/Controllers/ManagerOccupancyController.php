<?php

namespace App\Http\Controllers;

use App\Services\OccupancyService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class ManagerOccupancyController extends Controller
{
    /** GET /api/manager/occupancy?week=2026-W46 (zonder week: de huidige week) */
    public function show(Request $request, OccupancyService $occupancy): array
    {
        $request->validate([
            'week' => ['nullable', 'regex:/^\d{4}-W(0[1-9]|[1-4]\d|5[0-3])$/'],
        ]);

        $department = $request->user()->department;
        abort_unless($department, 404, 'Je hebt nog geen afdeling.');

        $date = CarbonImmutable::now();

        if ($week = $request->query('week')) {
            [$year, $number] = explode('-W', $week);
            $date = $date->setISODate((int) $year, (int) $number);
        }

        return $occupancy->forWeek($department, $date);
    }
}