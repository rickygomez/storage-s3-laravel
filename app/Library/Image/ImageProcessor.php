<?php

namespace App\Library\Image;

use App\DTOs\MakeImageDTO;
use Intervention\Image\Facades\Image;

class ImageProcessor implements ImageProcessorInterface
{
    private MakeImageDTO $imageSize;
    private string $imageContent;

    public function __construct()
    {
    }

    public function setImageSize(MakeImageDTO $imageSize): self
    {
        $this->imageSize = $imageSize;
        return $this;
    }

    public function setImageContent(string $imageContent): self
    {
        $this->imageContent = $imageContent;
        return $this;
    }

    private function validateImage($imageContent)
    {
        $tmpfname = tempnam(sys_get_temp_dir(), "FOO");
        $handle = fopen($tmpfname, "w");
        fwrite($handle, $imageContent);
        $size = getimagesize($tmpfname);

        if ($size === false) {
            throw new \Exception('Image not valid');
        }
    }

    public function makeImage(): string
    {
        $this->validateImage($this->imageContent);

        $processorImage = Image::make($this->imageContent);

        switch ($this->imageSize->processorType) {
            case 'resize':
                $processorImage->resize(
                    $this->imageSize->width,
                    $this->imageSize->heigth,
                    function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    }
                );
                break;
            default:
                $processorImage->fit(
                    $this->imageSize->width,
                    $this->imageSize->heigth
                );
                break;
        }

        $processorImage->encode(
            $this->imageSize->type,
            $this->imageSize->quality
        );

        return $processorImage->encoded;
    }
}
