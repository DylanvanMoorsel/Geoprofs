<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAbsenceRequest;
use App\Models\Absence;
use App\Services\AbsenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/** Verlof van de ingelogde medewerker. */
class AbsenceController extends Controller
{
    public function index(Request $request): array
    {
        return [
            'data' => $request->user()->absences()
                ->orderByDesc('start_date')
                ->orderByDesc('id')
                ->get()
                ->map->summary()
                ->all(),
        ];
    }

    public function store(StoreAbsenceRequest $request, AbsenceService $service): JsonResponse
    {
        $absence = $service->request($request->user(), $request->validated());

        return response()->json(['data' => $absence->summary()], 201);
    }

    public function destroy(Absence $absence, AbsenceService $service): Response
    {
        Gate::authorize('cancel', $absence);

        $service->cancel($absence);

        return response()->noContent();
    }
}