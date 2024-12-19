<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agent;
use App\Notifications\AccountActivated;

class AgentNotificationController extends Controller
{
    //    
    public function agentNotifications(Request $request)
    {
        $agent = Agent::find($request->user()->agent_id);

        if (!$agent) abort(404, 'Agent not found');

        return response()->json([
            'notifications' => $agent->notifications()
                ->latest()
                ->get(),
            'unread' => $agent->notifications()
                ->unread()
                ->count(),
        ]);
    }

    public function markAsRead(Request $request)
    {
        $agent = Agent::find($request->user()->agent_id);
        $input = $request->all();

        if (!$agent) {
            abort(404, 'Agent not found');
        }

        if (isset($input['id'])) {
            $agent->notifications()
                ->where('id', $input['id'])
                ->update(['read_at' => now()]);
        } else {
            $agent->notifications()
                ->unread()
                ->update(['read_at' => now()]);
        }

        return response()->json([
            'message' => 'Notifications marked as read',
            'unread' => $agent->notifications()
                ->unread()
                ->count(),
        ]);
    }

    //To be removed. Testing purpose only
    public function sendNotification(Request $request)
    {
        $agent = Agent::find($request->user()->agent_id);

        if (!$agent) {
            abort(404, 'Agent not found');
        }

        $device = $agent->devices()->active()->first();

        if ($device) {
            try {
                $device->notify(new AccountActivated());
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed to send notification',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            abort(404, 'No active device found');
        }
    }
}
