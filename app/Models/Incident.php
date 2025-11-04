<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'impact',
        'status',
        'started_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function monitors(): BelongsToMany
    {
        return $this->belongsToMany(Monitor::class, 'incident_monitors');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(IncidentUpdate::class);
    }

    public function isActive(): bool
    {
        return $this->status !== 'resolved';
    }

    public function resolve(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    public function getDurationInSeconds(): ?int
    {
        if (!$this->resolved_at) {
            return null;
        }

        return $this->started_at->diffInSeconds($this->resolved_at);
    }
}

