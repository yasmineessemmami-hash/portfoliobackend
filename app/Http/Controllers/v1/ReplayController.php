<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\ReplayEvent;
use App\Models\ReplaySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReplayController extends Controller
{
    /**
     * Start a new replay session
     * POST /api/replay/start
     */
    public function start(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userKey' => 'required|string|max:255',
            'appKey' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return errorResponse('Invalid parameters', 422);
        }

        $userKey = $request->input('userKey');
        $appKey = $request->input('appKey');
        $sessionId = ReplaySession::generateSessionId();

        // Create session record
        $session = ReplaySession::create([
            'session_id' => $sessionId,
            'user_key' => $userKey,
            'app_key' => $appKey,
            'created_at' => now(),
            'updated_at' => now(),
            'last_event_at' => now(),
            'event_count' => 0,
        ]);

        return successResponse([
            'sessionId' => $sessionId,
            'userKey' => $userKey,
            'appKey' => $appKey,
        ], 'Session started');
    }

    /**
     * Append events to a session
     * POST /api/replay/append
     */
    public function append(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sessionId' => 'required|string|max:64',
            'userKey' => 'required|string|max:255',
            'appKey' => 'required|string|max:255',
            'events' => 'required|array',
            'events.*' => 'required',
            'ts' => 'required|integer', // timestamp in milliseconds
        ]);

        if ($validator->fails()) {
            return errorResponse('Invalid parameters', 422);
        }

        $sessionId = $request->input('sessionId');
        $userKey = $request->input('userKey');
        $appKey = $request->input('appKey');
        $events = $request->input('events');
        $timestamp = $request->input('ts');

        // Verify session exists
        $session = ReplaySession::where('session_id', $sessionId)
            ->where('user_key', $userKey)
            ->where('app_key', $appKey)
            ->first();

        if (!$session) {
            return errorResponse('Session not found', 404);
        }

        // Store events
        ReplayEvent::create([
            'session_id' => $sessionId,
            'user_key' => $userKey,
            'app_key' => $appKey,
            'events' => $events,
            'timestamp' => $timestamp,
        ]);

        // Update session metadata
        $session->increment('event_count', count($events));
        $session->update(['last_event_at' => now()]);

        return successResponse(null, 'Events appended successfully');
    }

    /**
     * List sessions for admin
     * GET /api/replay/sessions
     */
    public function listSessions(Request $request)
    {
        $userKey = $request->input('userKey');
        $appKey = $request->input('appKey');
        $limit = min((int)$request->input('limit', 20), 100);
        $page = max((int)$request->input('page', 1), 1);

        $query = ReplaySession::query();

        if ($userKey) {
            $query->where('user_key', $userKey);
        }

        if ($appKey) {
            $query->where('app_key', $appKey);
        }

        $sessions = $query->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return successResponse([
            'data' => $sessions->items(),
            'current_page' => $sessions->currentPage(),
            'last_page' => $sessions->lastPage(),
            'per_page' => $sessions->perPage(),
            'total' => $sessions->total(),
        ], 'Sessions retrieved');
    }

    /**
     * Get session events for replay
     * GET /api/replay/session/:sessionId
     */
    public function getSession(Request $request, string $sessionId)
    {
        $session = ReplaySession::where('session_id', $sessionId)->first();

        if (!$session) {
            return errorResponse('Session not found', 404);
        }

        // Get all events for this session, ordered by timestamp
        $events = ReplayEvent::where('session_id', $sessionId)
            ->orderBy('timestamp')
            ->get()
            ->flatMap(function ($event) {
                return $event->events; // Flatten the events array
            })
            ->values()
            ->toArray();

        return successResponse([
            'sessionId' => $session->session_id,
            'userKey' => $session->user_key,
            'appKey' => $session->app_key,
            'createdAt' => $session->created_at,
            'lastEventAt' => $session->last_event_at,
            'eventCount' => $session->event_count,
            'events' => $events,
        ], 'Session retrieved');
    }

    /**
     * Delete a session and all its events
     * DELETE /api/replay/session/:sessionId
     */
    public function deleteSession(string $sessionId)
    {
        $session = ReplaySession::where('session_id', $sessionId)->first();

        if (!$session) {
            return errorResponse('Session not found', 404);
        }

        // Delete all events for this session
        ReplayEvent::where('session_id', $sessionId)->delete();

        // Delete the session
        $session->delete();

        return successResponse(null, 'Session deleted successfully');
    }
}
