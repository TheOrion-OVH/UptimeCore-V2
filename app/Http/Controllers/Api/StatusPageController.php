<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StatusPage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class StatusPageController extends Controller
{
    public function public(string $slug): JsonResponse
    {
        $statusPage = StatusPage::where('slug', $slug)
            ->with(['monitors', 'groups.monitors'])
            ->firstOrFail();

        $monitors = $statusPage->monitors()->get();
        $overallStatus = $this->calculateOverallStatus($monitors);

        return response()->json([
            'data' => [
                'name' => $statusPage->name,
                'description' => $statusPage->description,
                'overall_status' => $overallStatus,
                'logo_url' => $statusPage->logo_url,
                'color' => $statusPage->color,
                'groups' => $statusPage->groups->map(function ($group) {
                    return [
                        'name' => $group->name,
                        'monitors' => $group->monitors->map(function ($monitor) {
                            return [
                                'id' => $monitor->id,
                                'name' => $monitor->name,
                                'status' => $monitor->status,
                                'uptime_percentage' => $monitor->uptime_percentage,
                            ];
                        }),
                    ];
                }),
                'active_incidents' => [],
                'scheduled_maintenances' => [],
                'uptime_history' => [],
            ],
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        $statusPage = StatusPage::where('user_id', $request->user()->id)->first();

        if (!$statusPage) {
            $statusPage = StatusPage::create([
                'user_id' => $request->user()->id,
                'slug' => Str::slug($request->user()->name),
                'name' => 'Status - ' . $request->user()->name,
                'color' => config('status_page.default_color'),
            ]);
        }

        return response()->json([
            'data' => $statusPage,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $statusPage = StatusPage::where('user_id', $request->user()->id)->first();

        if (!$statusPage) {
            $statusPage = StatusPage::create([
                'user_id' => $request->user()->id,
                'slug' => Str::slug($request->user()->name),
            ]);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'color' => 'sometimes|string|max:7',
            'show_uptime' => 'sometimes|boolean',
            'history_days' => 'sometimes|integer|in:7,30,90',
        ]);

        $statusPage->update($validated);

        return response()->json([
            'data' => [
                'slug' => $statusPage->slug,
                'name' => $statusPage->name,
                'public_url' => $statusPage->public_url,
            ],
        ]);
    }

    private function calculateOverallStatus($monitors): string
    {
        $downCount = $monitors->where('status', 'down')->count();
        $totalCount = $monitors->count();

        if ($downCount === 0) {
            return 'operational';
        }

        if ($downCount === $totalCount) {
            return 'down';
        }

        return 'degraded';
    }
}

