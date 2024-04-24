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
        $billboard = Billboard::active()
            ->with(['images' => function ($query) {
                $query->select('id', 'billboard_id', 'image', 'is_active');
                $query->active();
            }])
            ->find($request->billboardId);

        if (!$billboard) abort(404, 'Billboard not found');

        return response()->json([
            'billboard' => $billboard
        ]);
    }

    public function agentBillboards(Request $request)
    {
        $agent = Agent::find($request->user()->agent_id);
        
        if (!$agent) abort(404, 'Agent not found');

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

        return response()->json([
            'billboards' => $agent->billboards()
                ->active()
                ->orderBy('updated_at', 'desc')
                ->get(),
            'districts' => $districts
        ]);
    }


    public function allBillboards(Request $request)
    {
        $agent = Agent::find($request->user()->agent_id);

        if (!$agent) abort(404, 'Agent not found');

        return response()->json([
            'billboards' => $agent->billboards()
                ->active()
                ->with(['images' => function ($query) {
                    $query->select('id', 'billboard_id', 'image', 'is_active');
                    $query->active();
                }])
                ->orderBy('updated_at', 'desc')
                ->paginate(5),
        ]);
    }

    public function agentBillboardsCoordinates(Request $request)
    {
        $agent = Agent::find($request->user()->agent_id);

        if (!$agent) abort(404, 'Agent not found');

        return response()->json([
            'billboardsCoordinates' => $agent->billboards()
                ->active()
                ->get([
                    'id',
                    'lat',
                    'lng',
                    'name',
                    'address',
                    'location',
                    'status',
                    'updated_at',
                ])
        ]);
    }
}
