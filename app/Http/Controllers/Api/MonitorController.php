<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Monitor;
use App\Jobs\CheckMonitorJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MonitorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Monitor::where('user_id', $request->user()->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('group')) {
            $query->where('group', $request->group);
        }

        $monitors = $query->paginate($request->get('per_page', 50));

        return response()->json([
            'data' => $monitors->items(),
            'meta' => [
                'total' => $monitors->total(),
                'per_page' => $monitors->perPage(),
                'current_page' => $monitors->currentPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:http,ping,tcp,dns,ssl',
            'url' => 'required_if:type,http,ssl|url',
            'host' => 'required_if:type,ping,tcp|string',
            'domain' => 'required_if:type,dns|string',
            'interval' => 'required|integer|min:30',
            'timeout' => 'required|integer|min:5|max:60',
            'retries' => 'integer|min:1|max:5',
            'group' => 'nullable|string|max:255',
            'notification_channels' => 'nullable|array',
            'notification_channels.*' => 'exists:notification_channels,id',
        ]);

        $monitor = Monitor::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'url' => $validated['url'] ?? null,
            'host' => $validated['host'] ?? null,
            'domain' => $validated['domain'] ?? null,
            'interval' => $validated['interval'],
            'timeout' => $validated['timeout'],
            'retries' => $validated['retries'] ?? 3,
            'group' => $validated['group'] ?? null,
            'status' => 'pending',
        ]);

        if (isset($validated['notification_channels'])) {
            $monitor->notificationChannels()->attach($validated['notification_channels']);
        }

        return response()->json([
            'data' => $monitor,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $monitor = Monitor::where('user_id', $request->user()->id)
            ->with(['notificationChannels', 'heartbeats' => function ($query) {
                $query->latest()->limit(100);
            }])
            ->findOrFail($id);

        return response()->json([
            'data' => $monitor,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $monitor = Monitor::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'interval' => 'sometimes|integer|min:30',
            'timeout' => 'sometimes|integer|min:5|max:60',
            'notification_channels' => 'nullable|array',
            'notification_channels.*' => 'exists:notification_channels,id',
        ]);

        $monitor->update($validated);

        if (isset($validated['notification_channels'])) {
            $monitor->notificationChannels()->sync($validated['notification_channels']);
        }

        return response()->json([
            'data' => $monitor,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $monitor = Monitor::where('user_id', $request->user()->id)->findOrFail($id);
        $monitor->delete();

        return response()->json([
            'message' => 'Monitor deleted successfully',
        ]);
    }

    public function pause(Request $request, int $id): JsonResponse
    {
        $monitor = Monitor::where('user_id', $request->user()->id)->findOrFail($id);
        $monitor->pause();

        return response()->json([
            'data' => $monitor,
        ]);
    }

    public function resume(Request $request, int $id): JsonResponse
    {
        $monitor = Monitor::where('user_id', $request->user()->id)->findOrFail($id);
        $monitor->resume();

        return response()->json([
            'data' => $monitor,
        ]);
    }

    public function check(Request $request, int $id): JsonResponse
    {
        $monitor = Monitor::where('user_id', $request->user()->id)->findOrFail($id);
        
        CheckMonitorJob::dispatch($monitor);

        return response()->json([
            'message' => 'Check queued successfully',
        ]);
    }

    public function stats(Request $request, int $id): JsonResponse
    {
        $monitor = Monitor::where('user_id', $request->user()->id)->findOrFail($id);

        $period = $request->get('period', '30d');
        $days = match ($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 30,
        };

        $startDate = now()->subDays($days);

        $heartbeats = $monitor->heartbeats()
            ->where('checked_at', '>=', $startDate)
            ->get();

        $totalChecks = $heartbeats->count();
        $successfulChecks = $heartbeats->where('status', 'up')->count();
        $failedChecks = $totalChecks - $successfulChecks;
        $uptimePercentage = $totalChecks > 0 ? ($successfulChecks / $totalChecks) * 100 : 0;
        $avgResponseTime = $heartbeats->where('status', 'up')->avg('response_time');

        $lastIncident = $monitor->incidents()
            ->where('status', 'resolved')
            ->latest('resolved_at')
            ->first();

        return response()->json([
            'data' => [
                'monitor_id' => $monitor->id,
                'period' => $period,
                'uptime_percentage' => round($uptimePercentage, 2),
                'avg_response_time' => round($avgResponseTime ?? 0),
                'total_checks' => $totalChecks,
                'successful_checks' => $successfulChecks,
                'failed_checks' => $failedChecks,
                'total_downtime_seconds' => $failedChecks * $monitor->interval,
                'incidents_count' => $monitor->incidents()->count(),
                'last_incident' => $lastIncident ? [
                    'started_at' => $lastIncident->started_at,
                    'resolved_at' => $lastIncident->resolved_at,
                    'duration_seconds' => $lastIncident->getDurationInSeconds(),
                ] : null,
            ],
        ]);
    }

    public function heartbeats(Request $request, int $id): JsonResponse
    {
        $monitor = Monitor::where('user_id', $request->user()->id)->findOrFail($id);

        $query = $monitor->heartbeats()->latest('checked_at');

        if ($request->has('from')) {
            $query->where('checked_at', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('checked_at', '<=', $request->to);
        }

        $heartbeats = $query->paginate($request->get('limit', 100));

        return response()->json([
            'data' => $heartbeats->items(),
            'meta' => [
                'total' => $heartbeats->total(),
                'per_page' => $heartbeats->perPage(),
                'current_page' => $heartbeats->currentPage(),
            ],
        ]);
    }

    public function uptimeDaily(Request $request, int $id): JsonResponse
    {
        $monitor = Monitor::where('user_id', $request->user()->id)->findOrFail($id);

        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);

        $heartbeats = $monitor->heartbeats()
            ->where('checked_at', '>=', $startDate)
            ->get()
            ->groupBy(function ($heartbeat) {
                return $heartbeat->checked_at->format('Y-m-d');
            });

        $data = [];
        foreach ($heartbeats as $date => $dayHeartbeats) {
            $totalChecks = $dayHeartbeats->count();
            $successfulChecks = $dayHeartbeats->where('status', 'up')->count();
            $failedChecks = $totalChecks - $successfulChecks;
            $uptimePercentage = $totalChecks > 0 ? ($successfulChecks / $totalChecks) * 100 : 0;

            $data[] = [
                'date' => $date,
                'uptime_percentage' => round($uptimePercentage, 2),
                'total_checks' => $totalChecks,
                'successful_checks' => $successfulChecks,
                'failed_checks' => $failedChecks,
            ];
        }

        return response()->json([
            'data' => $data,
        ]);
    }
}

