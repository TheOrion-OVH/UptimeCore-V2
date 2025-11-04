<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'status',
        'message',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}

