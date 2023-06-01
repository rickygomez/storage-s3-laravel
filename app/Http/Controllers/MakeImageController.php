<?php

namespace App\Http\Controllers;

use App\Http\Requests\MakeImageRequest;
use App\Library\Image\ImageProcessor;
use App\Library\Image\SaveImage;

class MakeImageController extends Controller
{
    public function __invoke(MakeImageRequest $request)
    {
        $file = $request->validated()['file'] ?? null;
        $url = $request->validated()['url'] ?? null;

        if ($url) {
            $imageContent = file_get_contents($url);
            $originalFileName = pathinfo($url)['filename'];
        }
        if ($file) {
            $imageContent = $file->get();
            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        }

        if ($imageContent) {
            $imageProcessor = new ImageProcessor();
            $saveImage = new SaveImage($imageProcessor);
            $images = $saveImage->setStorageDisk(config('sizeimage.storage_disk'))
                ->setImageContent($imageContent)
                ->saveImage($originalFileName);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Image created successfully',
            'data'      => $images
        ]);
    }
}
