<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class DeviceController extends Controller
{
    //

    public function ping(Request $request)
    {
        return response()->json([
            'message' => 'pong',
            'token' => $request->bearerToken()
        ]);
    }
}
