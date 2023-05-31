<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\DTOs\MakeImageDTO;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class DevPlayground extends Command
{
    protected $signature = 'dev:playground';

    protected $description = 'Command for testing purposes';

    private readonly MakeImageDTO $bigSize;
    private readonly MakeImageDTO $thumbSize;
    private readonly string $storageDisk;

    public function handle()
    {
        $this->bigSize = config('sizeimage.big_size');
        $this->thumbSize = config('sizeimage.thumb_size');
        $this->storageDisk = config('sizeimage.storage_disk');

        $url = 'https://assys.dev.br/rico/image-test/image-test-2bm.jpg';
        //$url = 'https://assys.dev.br/rico/image-test/no-image.jpg';
        $imageContent = file_get_contents($url);
        $checkImage = $this->validateImage($imageContent);

        if (!$checkImage) {
            dd('Image not valid');
        }

        $imageName = $this->getNameImage($url);
        $urlImage = $this->saveImage($imageName, $this->bigSize, $imageContent);
        $urlImageThumb = $this->saveImage($imageName, $this->thumbSize, $imageContent);
        dd($urlImage, $urlImageThumb);
    }

    private function validateImage($imageContent): bool
    {
        $tmpfname = tempnam(sys_get_temp_dir(), "FOO");
        $handle = fopen($tmpfname, "w");
        fwrite($handle, $imageContent);
        $size = getimagesize($tmpfname);

        return $size !== false;
    }

    private function getNameImage(string $url): string
    {
        $imagePathInfo = pathinfo($url);
        $originalFileName = $imagePathInfo['filename'];
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

    private function saveImage(string $imageName, MakeImageDTO $imageSetup, string $imageContent): string
    {
        $storage = Storage::disk($this->storageDisk);
        $pathImage = $imageSetup->folder . $imageSetup->prefix . $imageName;
        $imageContentProcessed = $this->processorImage($imageSetup, $imageContent);

        $storage->put($pathImage, $imageContentProcessed);
        return $storage->url($pathImage);
    }

    private function processorImage(MakeImageDTO $imageSetup, string $imageContent): string
    {
        $processorImage = Image::make($imageContent);

        switch ($imageSetup->processorType) {
            case 'resize':
                $processorImage->resize(
                    $imageSetup->width,
                    $imageSetup->heigth,
                    function ($constraint) use ($imageSetup) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    }
                );
                break;
            default:
                $processorImage->fit(
                    $imageSetup->width,
                    $imageSetup->heigth
                );
                break;
        }

        $processorImage->encode(
            $imageSetup->type,
            $imageSetup->quality
        );

        return $processorImage->encoded;
    }
}
