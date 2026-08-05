<?php

declare(strict_types=1);
namespace DogRaceFinder\Admin;

use DogRaceFinder\Cache\BreedCache;
use DogRaceFinder\Settings\SettingsRepository;

final class OverviewPage
{
    public function __construct(private SettingsRepository $settings, private BreedCache $cache, private View $view) {}

    public function render(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Du har ikke adgang til denne side.', '', ['response' => 403]);
        }
        $notice = get_transient('drf_notice_' . get_current_user_id());
        delete_transient('drf_notice_' . get_current_user_id());
        $this->view->render('overview', [
            'settings' => $this->settings->all(),
            'breeds' => $this->cache->get(),
            'status' => get_option('drf_api_status', []),
            'notice' => $notice,
        ]);
    }
}
