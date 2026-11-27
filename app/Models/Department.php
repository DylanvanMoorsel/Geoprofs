<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** Medewerkers van de afdeling, zonder managers. De bezetting rekent hierover. */
    public function employees(): HasMany
    {
        return $this->users()->where('role', '!=', User::ROLE_MANAGER);
    }
}