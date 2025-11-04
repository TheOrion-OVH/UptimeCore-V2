<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'name',
        'description',
        'logo_url',
        'color',
        'show_uptime',
        'history_days',
    ];

    protected function casts(): array
    {
        return [
            'show_uptime' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function monitors(): BelongsToMany
    {
        return $this->belongsToMany(Monitor::class, 'status_page_monitors');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(MonitorGroup::class);
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(StatusPageSubscriber::class);
    }

    public function getPublicUrlAttribute(): string
    {
        return url("/status/{$this->slug}");
    }
}

