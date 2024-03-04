<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OneTimePassword;
use App\Jobs\SendOneTimePassword;
use App\Models\Agent;
use App\Models\Device;
use Ramsey\Uuid\Uuid;

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
                'status' => 'pending',
                'expires_at' => now()->addMinutes(5),
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

        $otp = OneTimePassword::where([
            'phone_number' => $request->phone_number,
            'code' => $request->code,
            'status' => 'pending'
        ])->first();

        if (!$otp) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 404);
        }

        $otp->update([
            'phone_number_verified_at' => now(),
            'status' => 'used'
        ]);

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
                'uuid' =>   Uuid::uuid4(),
                'device_name' => $request->device_info['device_name'],
                'device_type' => $request->device_info['device_type'],
                'device_brand' => $request->device_info['device_brand'],
                'notification_token' => $request->notification_token,
                'ip_address' => $request->ip(),
            ]
        );

        $device->tokens()->delete();
        // $device->createToken($device->uuid, ['*'], now()->addWeek())->plainTextToken;
        $token = $device->createToken($device->uuid, ['*'], now()->addWeek())->plainTextToken;
        $device->update(['token' => $token]);

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
