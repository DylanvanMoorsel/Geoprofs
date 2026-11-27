<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    use HasFactory;

    public const TYPE_VACATION = 'vakantie';
    public const TYPE_SICK = 'ziekte';
    public const TYPE_APPOINTMENT = 'afspraak';
    public const TYPE_OTHER = 'overig';

    public const TYPES = [
        self::TYPE_VACATION,
        self::TYPE_SICK,
        self::TYPE_APPOINTMENT,
        self::TYPE_OTHER,
    ];

    public const STATUS_PENDING = 'aangevraagd';
    public const STATUS_APPROVED = 'goedgekeurd';
    public const STATUS_REJECTED = 'afgewezen';

    protected $fillable = [
        'user_id', 'type', 'start_date', 'end_date',
        'status', 'reason', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date:Y-m-d',
            'end_date' => 'date:Y-m-d',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeOverlapping($query, $from, $to)
    {
        return $query->whereDate('start_date', '<=', $to)
            ->whereDate('end_date', '>=', $from);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /** Werkdagen (ma t/m vr) tussen twee datums, grenzen inbegrepen. */
    public static function workdaysBetween($from, $to): int
    {
        $date = CarbonImmutable::parse($from)->startOfDay();
        $end = CarbonImmutable::parse($to)->startOfDay();
        $days = 0;

        while ($date <= $end) {
            if ($date->isWeekday()) {
                $days++;
            }
            $date = $date->addDay();
        }

        return $days;
    }

    public function workdays(): int
    {
        return self::workdaysBetween($this->start_date, $this->end_date);
    }

    public function hours(): float
    {
        return $this->workdays() * (float) config('occupancy.hours_per_day', 8);
    }

    /** Werkdagen van deze afwezigheid die in het opgegeven jaar vallen. */
    public function workdaysInYear(int $year): int
    {
        $from = $this->start_date->copy()->max(CarbonImmutable::create($year, 1, 1));
        $to = $this->end_date->copy()->min(CarbonImmutable::create($year, 12, 31));

        return $from > $to ? 0 : self::workdaysBetween($from, $to);
    }

    /** Vaste vorm voor de API. */
    public function summary(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'start_date' => $this->start_date->toDateString(),
            'end_date' => $this->end_date->toDateString(),
            'workdays' => $this->workdays(),
            'hours' => $this->hours(),
            'reason' => $this->reason,
            'reviewed_at' => $this->reviewed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /** Zelfde als summary(), met de medewerker erbij (voor de manager). */
    public function summaryWithEmployee(): array
    {
        return $this->summary() + [
            'employee' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'vacation_hours' => $this->user->vacation_hours,
            ],
        ];
    }
}