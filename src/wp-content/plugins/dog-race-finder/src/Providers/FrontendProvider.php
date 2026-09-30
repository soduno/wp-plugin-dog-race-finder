<?php

declare(strict_types=1);

namespace DogRaceFinder\Providers;

use DogRaceFinder\Frontend\Assets;
use DogRaceFinder\Frontend\Shortcodes;

final class FrontendProvider
{
    public function __construct(private Assets $assets, private Shortcodes $shortcodes) {}

    public function register(): void
    {
        $this->assets->register();
        $this->shortcodes->register();
    }
}
