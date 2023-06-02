<?php

namespace App\Library\Image;

use App\DTOs\MakeImageDTO;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SaveImage
{
    private MakeImageDTO $bigSize;
    private MakeImageDTO $thumbSize;
    private FilesystemAdapter $storage;
    private string $imageContent;
    private string $originalFileName;
    private string $saveFileName;

    public function __construct(
        private ImageProcessorInterface $imageProcessor,
    ) {
        $this->bigSize = config('sizeimage.big_size');
        $this->thumbSize = config('sizeimage.thumb_size');
    }

    public function setStorageDisk(string $storageDisk): self
    {
        $this->storage = Storage::disk($storageDisk);
        return $this;
    }

    public function setImageContent(string $imageContent): self
    {
        $this->imageContent = $imageContent;
        return $this;
    }

    public function saveImage(string $fileName): array
    {
        $this->originalFileName = $fileName;

        $processedImage = $this->imageProcessor->setImageSize($this->bigSize)
            ->setImageContent($this->imageContent)
            ->makeImage();
        $processedImageThumb = $this->imageProcessor->setImageSize($this->thumbSize)
            ->setImageContent($this->imageContent)
            ->makeImage();

        $this->saveFileName = $this->getNameImage($this->originalFileName);

        $urlImage = $this->storeImage($this->bigSize, $processedImage);
        $urlImageThumb = $this->storeImage($this->thumbSize, $processedImageThumb);

        return [
            'url_image' => $urlImage,
            'url_image_thumb' => $urlImageThumb,
        ];
    }

    private function getNameImage(): string
    {
        $fileName = $this->bigSize->prefix . $this->originalFileName;
        $extension = $this->bigSize->type;
        $path = $this->bigSize->folder;

        $hashUnique = $this->hashName($fileName, $extension, $path);

        $imageName = $this->originalFileName . $hashUnique . '.' . $extension;
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
        return $this->storage->exists($pathCheck);
    }

    private function storeImage(MakeImageDTO $imageSetup, string $imageContentProcessed): array
    {
        $pathImage = $imageSetup->folder . $imageSetup->prefix . $this->saveFileName;
        $this->storage->put($pathImage, $imageContentProcessed);
        return  [
            'url' => $this->storage->url($pathImage),
            'urlTemp' => $this->storage->temporaryUrl($pathImage, now()->addMinutes(10)),
            'path' => $pathImage,
        ];
    }
}
