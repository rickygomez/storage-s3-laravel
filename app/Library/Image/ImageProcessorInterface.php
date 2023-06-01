<?php

namespace App\Library\Image;

use App\DTOs\MakeImageDTO;

interface ImageProcessorInterface
{
    public function setImageSize(MakeImageDTO $imageSize): self;
    public function setImageContent(string $imageContent): self;
    public function makeImage(): string;
}
