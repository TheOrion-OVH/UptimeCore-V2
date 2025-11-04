<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusPageSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_page_id',
        'email',
        'webhook_url',
        'type',
    ];

    public function statusPage(): BelongsTo
    {
        return $this->belongsTo(StatusPage::class);
    }
}

