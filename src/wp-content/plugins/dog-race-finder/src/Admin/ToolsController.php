<?php

declare(strict_types=1);
namespace DogRaceFinder\Admin;

use DogRaceFinder\Api\DogApiClient;
use DogRaceFinder\Cache\BreedCache;
use DogRaceFinder\Cache\ImageCache;

final class ToolsController
{
    public function __construct(
        private DogApiClient $api,
        private BreedCache $cache,
        private ImageCache $imageCache,
    ) {}

    public function register(): void
    {
        add_action('admin_post_drf_refresh_breeds', [$this, 'refreshBreeds']);
        add_action('admin_post_drf_clear_cache', [$this, 'clearCache']);
    }

    public function refreshBreeds(): void
    {
        $this->authorize('drf_refresh_breeds');
        $result = $this->api->refreshBreeds();
        $this->redirect(is_wp_error($result) ? $result->get_error_message() : sprintf('Forbindelsen virker. %d racer og underracer blev hentet.', count($result)), is_wp_error($result));
    }

    public function clearCache(): void
    {
        $this->authorize('drf_clear_cache');
        $this->cache->clear();
        $this->imageCache->clear();
        $this->redirect('Racecachen er ryddet. Hent racelisten igen for at ændre racevalg.');
    }

    private function authorize(string $action): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Du har ikke adgang til denne handling.', '', ['response' => 403]);
        }
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            wp_die('Handlingen kræver POST.', '', ['response' => 405]);
        }
        check_admin_referer($action);
    }

    private function redirect(string $message, bool $error = false): never
    {
        set_transient('drf_notice_' . get_current_user_id(), ['message' => $message, 'error' => $error], MINUTE_IN_SECONDS);
        wp_safe_redirect(admin_url('admin.php?page=dog-race-finder'));
        exit;
    }
}
