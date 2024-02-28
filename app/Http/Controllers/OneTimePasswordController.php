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
            [
                'code' => $this->generateRandomNumber(),
                'status' => 'pending'
            ]
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
            'code' => 'required|numeric',
            'device_info' => 'required|array',
            'device_info.device_name' => 'string|nullable',
            'device_info.device_type' => 'string|nullable',
            'device_info.device_brand' => 'string|nullable',
            'notification_token' => 'string|nullable'
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

        $agent = Agent::where('phone_number', $request->phone_number)->first();

        if (!$agent) {
            return response()->json([
                'message' => 'No agent associated with this phone number',
                'agent' => null
            ], 200);
        }

        $device = Device::updateOrCreate(
            ['agent_id' => $agent->id],
            [
                'device_name' => $request->device_info['device_name'],
                'device_type' => $request->device_info['device_type'],
                'device_brand' => $request->device_info['device_brand'],
                'notification_token' => $request->notification_token,
                'ip_address' => $request->ip(),
            ]
        );

        $device->token = $device->createToken($agent->uuid)->plainTextToken;
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
