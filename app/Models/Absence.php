<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
 protected $fillable = [
    'user_id', 'type', 'start_date', 'end_date',
    'status', 'reason', 'reviewed_by', 'reviewed_at',
];

protected $casts = [
    'start_date' => 'date',
    'end_date' => 'date',
    'reviewed_at' => 'datetime',
];

public function user()
{
    return $this->belongsTo(User::class);
}

public function scopeApproved($query)
{
    return $query->where('status', 'goedgekeurd');
}

public function scopeOverlapping($query, $from, $to)
{
    return $query->where('start_date', '<=', $to)
                 ->where('end_date', '>=', $from);
}
}
