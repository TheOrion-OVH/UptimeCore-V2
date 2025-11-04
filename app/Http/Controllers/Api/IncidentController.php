<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentUpdate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class IncidentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Incident::where('user_id', $request->user()->id)
            ->with(['monitors', 'updates']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('monitor_id')) {
            $query->whereHas('monitors', function ($q) use ($request) {
                $q->where('monitors.id', $request->monitor_id);
            });
        }

        $incidents = $query->latest('started_at')->paginate(50);

        return response()->json([
            'data' => $incidents->items(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'impact' => 'required|in:minor,major,critical',
            'status' => 'sometimes|in:investigating,identified,monitoring,resolved',
            'monitor_ids' => 'nullable|array',
            'monitor_ids.*' => 'exists:monitors,id',
            'message' => 'nullable|string',
            'notify' => 'boolean',
        ]);

        $incident = Incident::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'impact' => $validated['impact'],
            'status' => $validated['status'] ?? 'investigating',
            'started_at' => now(),
        ]);

        if (isset($validated['monitor_ids'])) {
            $incident->monitors()->attach($validated['monitor_ids']);
        }

        if (isset($validated['message'])) {
            IncidentUpdate::create([
                'incident_id' => $incident->id,
                'status' => $incident->status,
                'message' => $validated['message'],
            ]);
        }

        return response()->json([
            'data' => $incident->load(['monitors', 'updates']),
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $incident = Incident::where('user_id', $request->user()->id)
            ->with(['monitors', 'updates'])
            ->findOrFail($id);

        return response()->json([
            'data' => $incident,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $incident = Incident::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|in:investigating,identified,monitoring,resolved',
            'message' => 'nullable|string',
        ]);

        if (isset($validated['status'])) {
            $incident->update(['status' => $validated['status']]);

            if ($validated['status'] === 'resolved' && !$incident->resolved_at) {
                $incident->update(['resolved_at' => now()]);
            }
        }

        if (isset($validated['message'])) {
            IncidentUpdate::create([
                'incident_id' => $incident->id,
                'status' => $incident->status,
                'message' => $validated['message'],
            ]);
        }

        return response()->json([
            'data' => $incident->load(['monitors', 'updates']),
        ]);
    }

    public function resolve(Request $request, int $id): JsonResponse
    {
        $incident = Incident::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'message' => 'nullable|string',
        ]);

        $incident->resolve();

        if (isset($validated['message'])) {
            IncidentUpdate::create([
                'incident_id' => $incident->id,
                'status' => 'resolved',
                'message' => $validated['message'],
            ]);
        }

        return response()->json([
            'data' => [
                'id' => $incident->id,
                'status' => $incident->status,
                'resolved_at' => $incident->resolved_at,
                'duration_seconds' => $incident->getDurationInSeconds(),
            ],
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $incident = Incident::where('user_id', $request->user()->id)->findOrFail($id);
        $incident->delete();

        return response()->json([
            'message' => 'Incident deleted successfully',
        ]);
    }
}

