<?php

declare(strict_types=1);
namespace DogRaceFinder\Admin;

final class SettingsPage
{
    public function __construct(private View $view) {}

    public function render(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Du har ikke adgang til denne side.', '', ['response' => 403]);
        }
        $this->view->render('settings');
    }
}
