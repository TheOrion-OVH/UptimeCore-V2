<?php

namespace App\Console\Commands;

use App\Models\Monitor;
use App\Jobs\CheckMonitorJob;
use Illuminate\Console\Command;

class ScheduleMonitorChecks extends Command
{
    protected $signature = 'monitors:check';
    protected $description = 'Run scheduled monitor checks';

    public function handle(): void
    {
        $monitors = Monitor::where('status', '!=', 'paused')
            ->where(function ($query) {
                $query->whereNull('last_check_at')
                    ->orWhereRaw('TIMESTAMPDIFF(SECOND, last_check_at, NOW()) >= interval');
            })
            ->get();

        foreach ($monitors as $monitor) {
            CheckMonitorJob::dispatch($monitor)->delay(now()->addSeconds(rand(0, 10)));
        }

        $this->info("Queued {$monitors->count()} monitor checks");
    }
}

