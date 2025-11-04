<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'starts_at',
        'ends_at',
        'started_at',
        'completed_at',
        'notify',
        'disable_alerts',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'notify' => 'boolean',
            'disable_alerts' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function monitors(): BelongsToMany
    {
        return $this->belongsToMany(Monitor::class, 'maintenance_monitors');
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function start(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function getDurationInMinutes(): int
    {
        return $this->starts_at->diffInMinutes($this->ends_at);
    }
}

