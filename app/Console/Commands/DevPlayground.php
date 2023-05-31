<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DevPlayground extends Command
{
    const STORAGE_DISK = 'image';
    private const BIG_SIZE = [
        'width' => '1000',
        'heigth' => '1000',
        'quality' => 60,
        'type' => 'jpg',
        'processor_type' => 'resize' // rezise, crop
    ];

    protected $signature = 'dev:playground';

    protected $description = 'Command for testing purposes';

    public function handle()
    {
        $storage = Storage::disk(self::STORAGE_DISK);

        $url = 'https://assys.dev.br/rico/image-test/image-test-2bm.jpg';
        $imageContent = file_get_contents($url);
        $imagePathInfo = pathinfo($url);
        $imageProperties = getimagesize($url);
        dump($imagePathInfo, $imageProperties);
        $imageName = $imagePathInfo['basename'];

        $imageContentProcessed = $this->processorImage(self::BIG_SIZE, $imageContent);

        $storage->put($imageName, $imageContentProcessed);
        $urlImage = $storage->url($imageName);

        dd($urlImage);
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
