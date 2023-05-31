<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DevPlayground extends Command
{
    const STORAGE_DISK = 'image';

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

        $storage->put($imageName, $imageContent);
        $urlImage = $storage->url($imageName);

        dd($urlImage);
    }
}
