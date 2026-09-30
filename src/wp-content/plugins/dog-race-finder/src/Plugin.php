<?php

declare(strict_types=1);

namespace DogRaceFinder;

use DogRaceFinder\Providers\AdminProvider;
use DogRaceFinder\Providers\FrontendProvider;
use DogRaceFinder\Providers\RestProvider;

final class Plugin
{
    public function __construct(
        private AdminProvider $admin,
        private FrontendProvider $frontend,
        private RestProvider $rest,
    ) {}

    public function register(): void
    {
        $this->admin->register();
        $this->frontend->register();
        $this->rest->register();
    }
}
