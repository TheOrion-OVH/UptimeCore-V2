<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MaintenanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Maintenance::where('user_id', $request->user()->id)
            ->with(['monitors']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $maintenances = $query->latest('starts_at')->paginate(50);

        return response()->json([
            'data' => $maintenances->items(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'monitor_ids' => 'nullable|array',
            'monitor_ids.*' => 'exists:monitors,id',
            'notify' => 'boolean',
            'disable_alerts' => 'boolean',
        ]);

        $startsAt = \Carbon\Carbon::parse($validated['starts_at']);
        $endsAt = $startsAt->copy()->addMinutes($validated['duration_minutes']);

        $maintenance = Maintenance::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'notify' => $validated['notify'] ?? true,
            'disable_alerts' => $validated['disable_alerts'] ?? true,
        ]);

        if (isset($validated['monitor_ids'])) {
            $maintenance->monitors()->attach($validated['monitor_ids']);
        }

        return response()->json([
            'data' => $maintenance->load('monitors'),
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $maintenance = Maintenance::where('user_id', $request->user()->id)
            ->with(['monitors'])
            ->findOrFail($id);

        return response()->json([
            'data' => $maintenance,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $maintenance = Maintenance::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'starts_at' => 'sometimes|date',
            'duration_minutes' => 'sometimes|integer|min:1',
            'description' => 'nullable|string',
        ]);

        if (isset($validated['starts_at']) || isset($validated['duration_minutes'])) {
            $startsAt = isset($validated['starts_at']) 
                ? \Carbon\Carbon::parse($validated['starts_at'])
                : $maintenance->starts_at;
            
            $duration = $validated['duration_minutes'] ?? $maintenance->getDurationInMinutes();
            $endsAt = $startsAt->copy()->addMinutes($duration);

            $maintenance->update([
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);
        }

        if (isset($validated['description'])) {
            $maintenance->update(['description' => $validated['description']]);
        }

        return response()->json([
            'data' => $maintenance->load('monitors'),
        ]);
    }

    public function start(Request $request, int $id): JsonResponse
    {
        $maintenance = Maintenance::where('user_id', $request->user()->id)->findOrFail($id);
        $maintenance->start();

        return response()->json([
            'data' => [
                'id' => $maintenance->id,
                'status' => $maintenance->status,
                'started_at' => $maintenance->started_at,
            ],
        ]);
    }

    public function complete(Request $request, int $id): JsonResponse
    {
        $maintenance = Maintenance::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'message' => 'nullable|string',
        ]);

        $maintenance->complete();

        return response()->json([
            'data' => [
                'id' => $maintenance->id,
                'status' => $maintenance->status,
                'completed_at' => $maintenance->completed_at,
            ],
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $maintenance = Maintenance::where('user_id', $request->user()->id)->findOrFail($id);
        $maintenance->delete();

        return response()->json([
            'message' => 'Maintenance deleted successfully',
        ]);
    }
}

