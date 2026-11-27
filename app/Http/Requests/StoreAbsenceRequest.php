<?php

namespace App\Http\Requests;

use App\Models\Absence;
use App\Services\AbsenceService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAbsenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        // Ziekmelden mag voor vandaag of met terugwerkende kracht, verlof niet.
        $startRules = $this->input('type') === Absence::TYPE_SICK ? [] : ['after_or_equal:today'];

        return [
            'type' => ['required', Rule::in(Absence::TYPES)],
            'start_date' => ['required', 'date_format:Y-m-d', ...$startRules],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Kies een geldig type verlof.',
            'start_date.after_or_equal' => 'De startdatum mag niet in het verleden liggen.',
            'end_date.after_or_equal' => 'De einddatum mag niet voor de startdatum liggen.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $user = $this->user();
                $from = $this->input('start_date');
                $to = $this->input('end_date');

                $workdays = Absence::workdaysBetween($from, $to);

                if ($workdays < 1) {
                    $validator->errors()->add('start_date', 'Kies minstens één werkdag (maandag t/m vrijdag).');

                    return;
                }

                $overlaps = $user->absences()
                    ->whereIn('status', [Absence::STATUS_PENDING, Absence::STATUS_APPROVED])
                    ->overlapping($from, $to)
                    ->exists();

                if ($overlaps) {
                    $validator->errors()->add('start_date', 'Je hebt in deze periode al verlof of een openstaande aanvraag.');

                    return;
                }

                if ($this->input('type') === Absence::TYPE_VACATION) {
                    $needed = $workdays * (float) config('occupancy.hours_per_day', 8);

                    if ($needed > app(AbsenceService::class)->availableVacationHours($user)) {
                        $validator->errors()->add('end_date', 'Je hebt niet genoeg verlofuren voor deze periode.');
                    }
                }
            },
        ];
    }
}