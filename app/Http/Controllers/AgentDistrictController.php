<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\AgentDistrict;
use App\Models\Billboard;
use Illuminate\Http\Request;

class AgentDistrictController extends Controller
{
    //
    public function agentDistricts(Request $request)
    {
        $agent = Agent::select('id', 'name', 'email', 'phone_number', 'status')
            ->find($request->user()->agent_id);

        $agentDistricts = AgentDistrict::where('agent_id', $agent->id)->with(
            'district:id,name'
        )->get();

        foreach ($agentDistricts as $agentDistrict) {
            $agentDistrict->billboards =
                Billboard::select('id', 'name', 'status', 'updated_at', 'lat', 'lng', 'location')
                ->active()->where(
                    'district_id',
                    $agentDistrict->district_id
                )
                ->where(
                    'agent_id',
                    $agent->id
                )
                ->active()
                ->get();
        }

        return response()->json([
            'agent_districts' => $agentDistricts
        ], 200);
    }
}
