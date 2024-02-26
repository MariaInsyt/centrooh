<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Device;
use App\Models\AgentDistrict;
use App\Models\Billboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    //
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email:rfc,dns|unique:agents',
            'phone_number' => 'required|numeric|unique:agents',
            'notification_token' => 'string|nullable',
            'device_info.device_name' => 'string|required',
            'device_info.device_type' => 'string|required',
            'device_info.uniqueId' => 'string|required',
        ]);

        //Todo: Implement OTP verification
        DB::transaction(function () use ($request, &$agent, &$token) {
            try {
                $agent = Agent::create([
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                ]);
                sleep(2);
                if ($agent) {
                    try {
                        $device = Device::create([
                            'agent_id' => $agent->id,
                            'device_id' => $request->device_info['uniqueId'],
                            'device_name' => $request->device_info['device_name'],
                            'device_type' => $request->device_info['device_type'],
                            'ip_address' => $request->ip(),
                            'notification_token' => $request->notification_token,
                        ]);
                        $token = $device->createToken($device->device_id)->plainTextToken;
                        $device->token = $token;
                        $device->save();
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => 'Device creation failed',
                            'error' => $e->getMessage()
                        ], 500);
                        DB::rollBack();
                    }
                }
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Agent creation failed',
                    'error' => $e->getMessage()
                ], 500);
                DB::rollBack();
            }
            DB::commit();

            return response()->json([
                'message' => 'Agent created successfully',
                'agent' => $agent,
                'token' => $token
            ], 201);
        });
    }

    public function agent(Request $request)
    {
        $agent = Agent::find($request->user()->agent_id);

        if (!$agent) {
            return response()->json([
                'message' => 'Agent not found'
            ], 404);
        }

        return response()->json([
            'agent' => $agent
        ], 200);
    }

    public function agentDistricts(Request $request)
    {
        $districts = AgentDistrict::where('agent_id', $request->user()->agent_id)
            ->with([
                'district'
            ])
            ->get();

        return response()->json([
            'districts' => $districts
        ]);
    }

    public function agentBillBoardsInDistrict(Request $request)
    {
        $district = $request->district_id;

        $billboards = Billboard::whereHas('district', function ($query) use ($district) {
            $query->where('district_id', $district);
        })
            ->where('agent_id', $request->user()->agent_id)
            ->get();

        return response()->json([
            'billboards' => $billboards
        ]);
    }
}
