<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\DTOs\MakeImageDTO;
use App\Library\Image\ImageProcessor;
use App\Library\Image\SaveImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DevPlayground extends Command
{
    protected $signature = 'dev:playground';

    protected $description = 'Command for testing purposes';

    public function handle()
    {
        $url = 'https://assys.dev.br/rico/image-test/image-test-2bm.jpg';
        //$url = 'https://assys.dev.br/rico/image-test/no-image.jpg';
        $imageContent = file_get_contents($url);
        $imagePathInfo = pathinfo($url);
        $originalFileName = $imagePathInfo['filename'];

        $imageProcessor = new ImageProcessor();
        $saveImage = new SaveImage($imageProcessor);
        $images = $saveImage->setStorageDisk(config('sizeimage.storage_disk'))
            ->setImageContent($imageContent)
            ->saveImage($originalFileName);

        dd($images);
    }
}
