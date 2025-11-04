<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Monitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'status',
        'url',
        'method',
        'headers',
        'body',
        'expected_status_code',
        'follow_redirects',
        'host',
        'packet_count',
        'port',
        'domain',
        'record_type',
        'expected_value',
        'days_before_alert',
        'interval',
        'timeout',
        'retries',
        'group',
        'response_time',
        'uptime_percentage',
        'total_checks',
        'successful_checks',
        'failed_checks',
        'last_check_at',
        'paused_at',
    ];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'follow_redirects' => 'boolean',
            'last_check_at' => 'datetime',
            'paused_at' => 'datetime',
            'uptime_percentage' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function heartbeats(): HasMany
    {
        return $this->hasMany(Heartbeat::class);
    }

    public function incidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'incident_monitors');
    }

    public function maintenances(): BelongsToMany
    {
        return $this->belongsToMany(Maintenance::class, 'maintenance_monitors');
    }

    public function notificationChannels(): BelongsToMany
    {
        return $this->belongsToMany(NotificationChannel::class, 'monitor_notification_channel')
            ->withPivot('notify_on_down', 'notify_on_up', 'delay_between_alerts');
    }

    public function statusPages(): BelongsToMany
    {
        return $this->belongsToMany(StatusPage::class, 'status_page_monitors');
    }

    public function isUp(): bool
    {
        return $this->status === 'up';
    }

    public function isDown(): bool
    {
        return $this->status === 'down';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function pause(): void
    {
        $this->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);
    }

    public function resume(): void
    {
        $this->update([
            'status' => 'pending',
            'paused_at' => null,
        ]);
    }
}

