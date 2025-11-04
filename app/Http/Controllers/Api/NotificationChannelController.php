<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationChannel;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationChannelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $channels = NotificationChannel::where('user_id', $request->user()->id)->get();

        return response()->json([
            'data' => $channels,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:email,discord,webhook',
            'label' => 'required|string|max:255',
            'config' => 'required|array',
        ]);

        $channel = NotificationChannel::create([
            'user_id' => $request->user()->id,
            'type' => $validated['type'],
            'label' => $validated['label'],
            'config' => $validated['config'],
            'enabled' => true,
        ]);

        return response()->json([
            'data' => $channel,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $channel = NotificationChannel::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json([
            'data' => $channel,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $channel = NotificationChannel::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'label' => 'sometimes|string|max:255',
            'config' => 'sometimes|array',
            'enabled' => 'sometimes|boolean',
        ]);

        $channel->update($validated);

        return response()->json([
            'data' => $channel,
        ]);
    }

    public function test(Request $request, int $id): JsonResponse
    {
        $channel = NotificationChannel::where('user_id', $request->user()->id)->findOrFail($id);

        try {
            $channel->notify(new TestNotification());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Test notification failed: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Test notification sent successfully',
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $channel = NotificationChannel::where('user_id', $request->user()->id)->findOrFail($id);
        $channel->delete();

        return response()->json([
            'message' => 'Notification channel deleted successfully',
        ]);
    }
}

