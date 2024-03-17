<?php

namespace App\Http\Controllers;

use App\Models\Billboard;
use App\Models\Agent;
use App\Models\AgentDistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BillboardController extends Controller
{
    //
    public function billboard(Request $request)
    {
        $billboard = Billboard::active()->find($request->billboardId);

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

        $agentDistricts = AgentDistrict::where('agent_id', $agent->id)->with(
            'district:id,name'
        )->get();

        $districts = $agentDistricts->pluck('district');
        $districts = $districts->map(function ($district) {
            return [
                'value' => $district['id'],
                'label' => $district['name']
            ];
        });

        if ($agent) {
            return response()->json([
                'billboards' => $agent->billboards()
                    ->active()
                    ->orderBy('updated_at', 'desc')
                    ->get(),
                'districts' => $districts
            ]);
        } else {
            return response()->json([
                'message' => 'Agent not found'
            ], 404);
        }
    }

    public function agentBillboardsCoordinates(Request $request)
    {
        $agent = Agent::find($request->user()->agent_id);

        if ($agent) {
            $billboardsCoordinates = Cache::remember('agentBillboardsCoordinates', 25 * 60, function () use ($agent) {
                return $agent->billboards()
                    ->active()
                    ->get([
                        'id',
                        'lat',
                        'lng',
                        'name',
                        'address',
                        'status',
                        'updated_at',
                    ]);
            });

            return response()->json([
                'billboardsCoordinates' => $billboardsCoordinates
            ]);
        } else {
            return response()->json([
                'message' => 'Agent not found'
            ], 404);
        }
    }
}
