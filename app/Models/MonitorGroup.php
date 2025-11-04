<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MonitorGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_page_id',
        'name',
        'order',
    ];

    public function statusPage(): BelongsTo
    {
        return $this->belongsTo(StatusPage::class);
    }

    public function monitors(): BelongsToMany
    {
        return $this->belongsToMany(Monitor::class, 'monitor_group_monitors');
    }
}

