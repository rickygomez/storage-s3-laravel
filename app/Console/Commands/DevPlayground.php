<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class DevPlayground extends Command
{
    const STORAGE_DISK = 'image';
    private const BIG_SIZE = [
        'width' => '1000',
        'heigth' => '1000',
        'quality' => 60,
        'type' => 'jpg',
        'prefix' => 'big_',
        'folder' => '',
        'processor_type' => 'resize' // rezise, crop
    ];

    private const THUMBNAIL_SIZE = [
        'width' => '300',
        'heigth' => '200',
        'quality' => 50,
        'type' => 'jpg',
        'prefix' => 'thumb_',
        'folder' => 'thumb/',
        'processor_type' => 'crop' // rezise, crop
    ];

    protected $signature = 'dev:playground';

    protected $description = 'Command for testing purposes';

    public function handle()
    {
        $url = 'https://assys.dev.br/rico/image-test/image-test-2bm.jpg';
        $imageContent = file_get_contents($url);

        $imageName = $this->getNameImage($url);
        $urlImage = $this->saveImage($imageName, self::BIG_SIZE, $imageContent);
        $urlImageThumb = $this->saveImage($imageName, self::THUMBNAIL_SIZE, $imageContent);

        dd($urlImage, $urlImageThumb);
    }

    private function getNameImage(string $url): string
    {
        $imagePathInfo = pathinfo($url);
        $originalFileName = $imagePathInfo['filename'];
        $fileName = self::BIG_SIZE['prefix'] . $originalFileName;
        $extension = self::BIG_SIZE['type'];
        $path = self::BIG_SIZE['folder'];

        $hashUnique = $this->hashName($fileName, $extension, $path );

        $imageName = $originalFileName . $hashUnique . '.' . $extension;
        return $imageName;
    }

    private function hashName(string $fileName, string $extension, string $path = ''):string|null
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
        return Storage::disk(self::STORAGE_DISK)->exists($pathCheck);

    }

    private function saveImage(string $imageName, array $imageSetup, string $imageContent):string
    {
        $storage = Storage::disk(self::STORAGE_DISK);
        $pathImage = $imageSetup['folder'] . $imageSetup['prefix'] . $imageName;
        $imageContentProcessed = $this->processorImage($imageSetup, $imageContent);

        $storage->put($pathImage, $imageContentProcessed);
        return $storage->url($pathImage);
    }

    private function processorImage(array $imageSetup, string $imageContent): string
    {
        $processorImage = Image::make($imageContent);

        switch ($imageSetup['processor_type']) {
            case 'resize':
                $processorImage->resize(
                    $imageSetup['width'],
                    $imageSetup['heigth'],
                    function ($constraint) use ($imageSetup) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    }
                );
                break;
            default:
                $processorImage->fit(
                    $imageSetup['width'],
                    $imageSetup['heigth']
                );
                break;
        }

        $processorImage->encode(
            $imageSetup['type'],
            $imageSetup['quality']
        );

        return $processorImage->encoded;
    }
}
