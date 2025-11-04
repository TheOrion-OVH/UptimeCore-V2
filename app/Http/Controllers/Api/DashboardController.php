<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Monitor;
use App\Models\Incident;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $monitors = Monitor::where('user_id', $userId)->get();
        $totalMonitors = $monitors->count();
        $monitorsUp = $monitors->where('status', 'up')->count();
        $monitorsDown = $monitors->where('status', 'down')->count();
        $monitorsPaused = $monitors->where('status', 'paused')->count();

        $overallUptime = $monitors->sum('uptime_percentage') / max($totalMonitors, 1);
        $avgResponseTime = $monitors->where('status', 'up')->avg('response_time');

        $activeIncidents = Incident::where('user_id', $userId)
            ->where('status', '!=', 'resolved')
            ->count();

        $scheduledMaintenances = Maintenance::where('user_id', $userId)
            ->where('status', 'scheduled')
            ->count();

        return response()->json([
            'data' => [
                'total_monitors' => $totalMonitors,
                'monitors_up' => $monitorsUp,
                'monitors_down' => $monitorsDown,
                'monitors_paused' => $monitorsPaused,
                'overall_uptime' => round($overallUptime, 2),
                'avg_response_time' => round($avgResponseTime ?? 0),
                'active_incidents' => $activeIncidents,
                'scheduled_maintenances' => $scheduledMaintenances,
            ],
        ]);
    }
}

