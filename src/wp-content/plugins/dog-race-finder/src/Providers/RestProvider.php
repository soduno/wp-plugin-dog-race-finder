<?php

declare(strict_types=1);

namespace DogRaceFinder\Providers;

use DogRaceFinder\Rest\GalleryController;

final class RestProvider
{
    public function __construct(private GalleryController $gallery) {}

    public function register(): void
    {
        $this->gallery->register();
    }
}
