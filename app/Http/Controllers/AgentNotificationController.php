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
        $agent = Agent::find($request->user()->agent_id);
        $input = $request->all();
        
        if ($agent){
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
        } else {
            return response()->json([
                'message' => 'Agent not found'
            ], 404);
        }
    }

    //To be removed. Testing purpose only
    public function sendNotification(Request $request)
    {
        $agent = Agent::find($request->user()->agent_id);

        if ($agent) {
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
                return response()->json([
                    'message' => 'Device not found',
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Agent not found'
            ], 404);
        }

    }
}
