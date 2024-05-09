<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billboard;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Typography\FontFactory;
use Intervention\Image\Geometry\Factories\RectangleFactory;

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

        $billboardInfo = [
            'name' => $billboard->name,
            'district' => $billboard->district->name,
            'latitude' => $billboard->lat,
            'longitude' => $billboard->lng,
        ];

        if (!$billboard) abort(404, 'Billboard not found');

        $image = $request->file('image');
        $imageName = Str::snake($billboard->name) . '_' . str_replace(" ", "", date("d-m-Y h:i:s")) . '.' . $image->extension();
        $formattedImageName = $this->addTextToImage($image, $billboardInfo);
        $image = public_path('images/' . $formattedImageName);

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

    public function addTextToImage(
        $image,
        $billboardInfo = []
    ) {
        $img = Image::read($image);

        $imgWidth = $img->width();
        $imageHeight = $img->height();

        $x = 0;
        $y = $imageHeight - 160;
        $currentDate = date('Y-m-d(D) H:i');
        $imageName = str_replace(" ", "", $billboardInfo['name']) . '_' . str_replace(" ", "", date("d-m-Y h:i:s")) . '.' . $image->extension();

        $text = <<<EOT
                {$billboardInfo['district']} \n
                Latitude: {$billboardInfo['latitude']}
                Longitude: {$billboardInfo['longitude']} \n
                $currentDate
                EOT;

        $img->drawRectangle(
            $x,
            $y,
            function (RectangleFactory $rectangle) use ($imgWidth) {
                $rectangle->size($imgWidth, 160);
                $rectangle->background('#0000');
            }
        );
        $img->text($text, $imgWidth / 2, $y + 60, function (FontFactory $font) {
            $font->filename(public_path('assets/fonts/Exo2-Bold.otf'));
            $font->size(38);
            $font->color('#FFDB58');
            $font->align('center');
            $font->valign('center');
        });
        $img->save(public_path('images/' . $imageName));

        return $imageName;
    }
}
