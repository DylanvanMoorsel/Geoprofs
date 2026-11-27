<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Services\AbsenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Aanvragen beoordelen. Een manager ziet en beoordeelt alleen aanvragen van
 * medewerkers die aan hem of haar rapporteren (users.manager_id).
 */
class ManagerAbsenceController extends Controller
{
    public function index(Request $request): array
    {
        $data = $request->validate([
            'status' => ['nullable', Rule::in([
                Absence::STATUS_PENDING,
                Absence::STATUS_APPROVED,
                Absence::STATUS_REJECTED,
            ])],
        ]);

        $absences = Absence::with('user')
            ->where('status', $data['status'] ?? Absence::STATUS_PENDING)
            ->whereHas('user', fn ($query) => $query->where('manager_id', $request->user()->id))
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();

        return ['data' => $absences->map->summaryWithEmployee()->all()];
    }

    public function approve(Request $request, Absence $absence, AbsenceService $service): array
    {
        Gate::authorize('review', $absence);

        $absence = $service->approve($absence, $request->user())->load('user');

        return ['data' => $absence->summaryWithEmployee()];
    }

    public function reject(Request $request, Absence $absence, AbsenceService $service): array
    {
        Gate::authorize('review', $absence);

        $absence = $service->reject($absence, $request->user())->load('user');

        return ['data' => $absence->summaryWithEmployee()];
    }

    /** Meerdere aanvragen tegelijk. Een mislukte stopt de rest niet. */
    public function bulk(Request $request, AbsenceService $service): array
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['approve', 'reject'])],
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct'],
        ]);

        $absences = Absence::with('user')->whereIn('id', $data['ids'])->get()->keyBy('id');
        $processed = [];
        $failed = [];

        foreach ($data['ids'] as $id) {
            $absence = $absences->get($id);

            if (! $absence) {
                $failed[] = ['id' => $id, 'message' => 'Aanvraag niet gevonden.'];

                continue;
            }

            if (Gate::denies('review', $absence)) {
                $failed[] = ['id' => $id, 'message' => 'Geen toegang tot deze aanvraag.'];

                continue;
            }

            try {
                $data['action'] === 'approve'
                    ? $service->approve($absence, $request->user())
                    : $service->reject($absence, $request->user());

                $processed[] = $id;
            } catch (ValidationException $e) {
                $failed[] = ['id' => $id, 'message' => collect($e->errors())->flatten()->first()];
            }
        }

        return ['processed' => $processed, 'failed' => $failed];
    }
}