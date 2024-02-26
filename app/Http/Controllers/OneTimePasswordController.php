<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OneTimePassword;
use App\Jobs\SendOneTimePassword;
use App\Models\Agent;
use App\Models\Device;

class OneTimePasswordController extends Controller
{
    //
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|numeric'
        ]);

        $code = OneTimePassword::updateOrCreate(
            ['phone_number' => $request->phone_number],
            ['code' => $this->generateRandomNumber()]
        );

        SendOneTimePassword::dispatch($code);

        return response()->json([
            'message' => 'Code sent successfully'
        ], 200);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|numeric',
            'code' => 'required|numeric'
        ]);

        $otp = OneTimePassword::where('phone_number', $request->phone_number)
            ->where('code', $request->code)
            ->where('status', 'pending')
            ->first();

        if (!$otp) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 404);
        }

        $otp->status = 'used';
        $otp->save();
        $otp->delete();

        $agent = Agent::where('phone_number', $request->phone_number)->first();

        if (!$agent) {
            return response()->json([
                'message' => 'No agent associated with this phone number',
                'agent' => null
            ], 200);
        }

        $device = Device::updateOrCreate(
            ['device_id' => $request->device_info['uniqueId']],
            [
                'device_name' => $request->device_info['deviceName'],
                'device_type' => $request->device_info['deviceType'],
                'ip_address' => $request->ip(),
                'agent_id' => $agent->id
            ]
        );

        $device->token = $device->createToken($device->device_id)->plainTextToken;
        $device->save();

        return response()->json([
            'message' => 'OTP validated successfully',
            'agent' => $agent,
            'token' => $device->token
        ], 200);
    }

    public function generateRandomNumber()
    {
        $randomNumber = mt_rand(100000, 999999);
        return $randomNumber;
    }
}
