<?php

namespace App\Http\Controllers;

use App\Models\Billboard;
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
}
