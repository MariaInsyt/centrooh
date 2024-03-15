<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentNotification;
use App\Models\Agent;

class AgentNotificationController extends Controller
{
    //

    public function agentNotifications(Request $request)
    {
        $agent = Agent::find($request->user()->agent_id);

        if ($agent) {
            return response()->json([
                'notifications' => $agent->notifications()
                    ->latest()
                    ->get(),
                'unread' => $agent->notifications()
                    ->unread()
                    ->count(),
            ]);
        } else {
            return response()->json([
                'message' => 'Agent not found'
            ], 404);
        }
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $agent = Agent::find($request->user()->agent_id);

        if ($agent) {
            $agent->notifications()
                ->whereIn('id', $request->ids)
                ->update(['read_at' => now()]);

            return response()->json([
                'message' => 'Notifications marked as read'
            ]);
        } else {
            return response()->json([
                'message' => 'Agent not found'
            ], 404);
        }

    }
}
