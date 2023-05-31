<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\PlaygroundApiRequest;

class MakeImageDTO extends AbstractDTO
{
    public function __construct(
        public readonly int $width,
        public readonly int $heigth,
        public readonly int $quality,
        public readonly string $type,
        public readonly string $prefix,
        public readonly string $folder,
        public readonly string $processorType // rezise, crop
    ) {
    }

    public static function makeFromArray($attributes): self
    {
        return new self(
            $attributes['width'],
            $attributes['heigth'],
            $attributes['quality'],
            $attributes['type'],
            $attributes['prefix'],
            $attributes['folder'],
            $attributes['processorType']
        );
    }
}
