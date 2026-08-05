<?php

declare(strict_types=1);
namespace DogRaceFinder\Admin;

final class Menu
{
    public function __construct(private OverviewPage $overview, private SettingsPage $settings) {}

    public function register(): void
    {
        add_action('admin_menu', [$this, 'addPages']);
    }

    public function addPages(): void
    {
        add_menu_page('Dog Race Finder', 'Dog Race Finder', 'manage_options', 'dog-race-finder', [$this->overview, 'render'], 'dashicons-pets', 58);
        add_submenu_page('dog-race-finder', 'Oversigt', 'Oversigt', 'manage_options', 'dog-race-finder', [$this->overview, 'render']);
        add_submenu_page('dog-race-finder', 'Indstillinger', 'Indstillinger', 'manage_options', 'dog-race-finder-settings', [$this->settings, 'render']);
    }
}
