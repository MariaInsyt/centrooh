<?php

namespace App\Http\Controllers;

use App\Models\Billboard;
use App\Models\Agent;
use Illuminate\Http\Request;

class BillboardController extends Controller
{
    //
    public function billboard(Request $request)
    {
        $billboard = Billboard::active()->find($request->billboard_id);

        if ($billboard) {
            return response()->json([
                'billboard' => $billboard
            ]);
        } else {
            return response()->json([
                'message' => 'Billboard not found'
            ], 404);
        }
    }

    public function agentBillboards(Request $request)
    {
        $agent = Agent::find($request->user()->agent_id);

        if ($agent) {
            return response()->json([
                'billboards' => $agent->billboards()
                    ->active()
                    ->orderBy('updated_at', 'desc')
                    ->with('district:id,name')
                    ->get()

            ]);
        } else {
            return response()->json([
                'message' => 'Agent not found'
            ], 404);
        }
    }
}
