<?php

declare(strict_types=1);

namespace DogRaceFinder\Providers;

use DogRaceFinder\Admin\Menu;
use DogRaceFinder\Admin\SettingsRegistration;
use DogRaceFinder\Admin\ToolsController;

final class AdminProvider
{
    public function __construct(
        private Menu $menu,
        private SettingsRegistration $settings,
        private ToolsController $tools,
    ) {}

    public function register(): void
    {
        $this->menu->register();
        $this->settings->register();
        $this->tools->register();
    }
}
