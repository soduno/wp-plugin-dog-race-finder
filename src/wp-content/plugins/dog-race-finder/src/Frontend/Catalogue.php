<?php

declare(strict_types=1);
namespace DogRaceFinder\Frontend;

use DogRaceFinder\Api\DogApiClient;
use DogRaceFinder\Cache\BreedCache;
use DogRaceFinder\Settings\SettingsRepository;
use WP_Error;

final class Catalogue
{
    public function __construct(private DogApiClient $api, private BreedCache $cache, private SettingsRepository $settings) {}

    public function breeds(): array|WP_Error
    {
        $breeds = $this->cache->get() ?? $this->api->refreshBreeds();
        if (is_wp_error($breeds)) {
            return new WP_Error('drf_catalogue', $breeds->get_error_message(), ['status' => 502]);
        }
        $allowed = $this->settings->all()['allowed_breeds'];
        return $allowed === [] ? $breeds : array_intersect_key($breeds, array_flip($allowed));
    }
}
