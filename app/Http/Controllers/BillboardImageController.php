<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billboard;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;

class BillboardImageController extends Controller
{
    //
    public function storeBillboardImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg',
            'id' => 'required|exists:billboards,id',
        ]);

        $billboard = Billboard::active()->find($request->id);

        if (!$billboard) abort(404, 'Billboard not found');

        $image = $request->file('image');
        $imageName = Str::snake($billboard->name) . '_' . str_replace(" ", "", date("d-m-Y h:i:s")) . '.' . $image->extension();

        try {
            $path = Storage::putFileAs('billboards', new  File($image), $imageName, 'public');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Image upload failed.',
                'error' => $e->getMessage(),
            ], 422);
        }

        if ($path) {
            $billboard->images()->create([
                'image' => $path,
                'is_active' => 0,
            ]);

            $billboard->update([
                'status' => 'in_review',
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Image uploaded successfully.',
        ], 201);
    }
}
