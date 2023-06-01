<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\DTOs\MakeImageDTO;
use App\Library\Image\ImageProcessor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DevPlayground extends Command
{
    protected $signature = 'dev:playground';

    protected $description = 'Command for testing purposes';

    private MakeImageDTO $bigSize;
    private MakeImageDTO $thumbSize;
    private string $storageDisk;
    private string $imageContent;

    public function handle()
    {
        $this->bigSize = config('sizeimage.big_size');
        $this->thumbSize = config('sizeimage.thumb_size');
        $this->storageDisk = config('sizeimage.storage_disk');

        $url = 'https://assys.dev.br/rico/image-test/image-test-2bm.jpg';
        //$url = 'https://assys.dev.br/rico/image-test/no-image.jpg';
        $imageContent = file_get_contents($url);

        $imageProcessor = new ImageProcessor();
        $processedImage = $imageProcessor->setImageSize($this->bigSize)
            ->setImageContent($imageContent)
            ->makeImage();
        $processedImageThumb = $imageProcessor->setImageSize($this->thumbSize)
            ->setImageContent($imageContent)
            ->makeImage();

        $imagePathInfo = pathinfo($url);
        $originalFileName = $imagePathInfo['filename'];

        $imageName = $this->getNameImage($originalFileName);
        $urlImage = $this->saveImage($imageName, $this->bigSize, $processedImage);
        $urlImageThumb = $this->saveImage($imageName, $this->thumbSize, $processedImageThumb);
        dd($urlImage, $urlImageThumb);
    }

    private function getNameImage(string $originalFileName): string
    {
        $fileName = $this->bigSize->prefix . $originalFileName;
        $extension = $this->bigSize->type;
        $path = $this->bigSize->folder;

        $hashUnique = $this->hashName($fileName, $extension, $path);

        $imageName = $originalFileName . $hashUnique . '.' . $extension;
        return $imageName;
    }

    private function hashName(string $fileName, string $extension, string $path = ''): string|null
    {
        $pathCheck = $path . $fileName . '.' . $extension;
        $hash = null;

        while ($this->checkExist($pathCheck)) {
            $hash = '_' . Str::random(10);
            $pathCheck = $path . $fileName . $hash . '.' . $extension;
        }

        return $hash;
    }

    private function checkExist($pathCheck): bool
    {
        return Storage::disk($this->storageDisk)->exists($pathCheck);
    }

    private function saveImage(string $imageName, MakeImageDTO $imageSetup, string $imageContentProcessed): string
    {
        $storage = Storage::disk($this->storageDisk);
        $pathImage = $imageSetup->folder . $imageSetup->prefix . $imageName;

        $storage->put($pathImage, $imageContentProcessed);
        return $storage->url($pathImage);
    }
}
