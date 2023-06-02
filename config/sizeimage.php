<?php

declare(strict_types=1);

use App\DTOs\MakeImageDTO;

$bigSize = MakeImageDTO::makeFromArray([
    'width' => 1000,
    'heigth' => 1000,
    'quality' => 60,
    'type' => 'jpg',
    'prefix' => '',
    'folder' => '',
    'processorType' => 'resize' // rezise, crop
]);

$thumbnailSize = MakeImageDTO::makeFromArray([
    'width' => 300,
    'heigth' => 200,
    'quality' => 50,
    'type' => 'jpg',
    'prefix' => 'thumb_',
    'folder' => 'thumb/',
    'processorType' => 'crop' // rezise, crop
]);

return [

    'storage_disk' => 'image-s3',
    'big_size' => $bigSize,
    'thumb_size' => $thumbnailSize

];
