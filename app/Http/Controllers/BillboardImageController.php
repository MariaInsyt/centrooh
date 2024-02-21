<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billboard;
use App\Models\BillboardImage;

class BillboardImageController extends Controller
{
    //

    public function insert()
    {
        //delete all images
        BillboardImage::truncate();

        $billboard = Billboard::find(1);
        $billboard->images()->create([
            'image' => 'https://cdn.quasar.dev/img/mountains.jpg'
        ]);

        return 'Image has been inserted';
    }
}
