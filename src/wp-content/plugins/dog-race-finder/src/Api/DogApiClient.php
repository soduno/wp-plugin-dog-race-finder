<?php

declare(strict_types=1);
namespace DogRaceFinder\Api;

use DogRaceFinder\Cache\BreedCache;
use DogRaceFinder\Settings\SettingsRepository;
use WP_Error;

final class DogApiClient
{
    public function __construct(private BreedCache $cache, private SettingsRepository $settings) {}

    public function refreshBreeds(): array|WP_Error
    {
        $response = wp_remote_get(\DogRaceFinder\API_BASE_URL . 'breeds/list/all', [
            'timeout' => 10,
            'redirection' => 0,
            'limit_response_size' => 262144,
        ]);
        if (is_wp_error($response)) {
            return new WP_Error('drf_connection', 'Kunne ikke oprette forbindelse til Dog CEO. Prøv igen senere.');
        }
        if (wp_remote_retrieve_response_code($response) !== 200) {
            return new WP_Error('drf_http', 'Dog CEO returnerede en fejl. Prøv igen senere.');
        }
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($data) || ($data['status'] ?? '') !== 'success' || !is_array($data['message'] ?? null) || $data['message'] === []) {
            return new WP_Error('drf_response', 'API-svaret indeholdt ikke en gyldig raceliste.');
        }
        $breeds = [];
        foreach ($data['message'] as $breed => $subBreeds) {
            if (!is_string($breed) || !preg_match('/^[a-z]+$/D', $breed) || !is_array($subBreeds)) {
                return new WP_Error('drf_breed', 'API-svaret indeholdt ugyldige racedata.');
            }
            $breeds[$breed] = ucfirst($breed);
            foreach ($subBreeds as $subBreed) {
                if (!is_string($subBreed) || !preg_match('/^[a-z]+$/D', $subBreed)) {
                    return new WP_Error('drf_breed', 'API-svaret indeholdt ugyldige racedata.');
                }
                $breeds[$breed . '/' . $subBreed] = ucfirst($subBreed) . ' ' . ucfirst($breed);
            }
        }
        asort($breeds, SORT_NATURAL | SORT_FLAG_CASE);
        $this->cache->put($breeds, (int) $this->settings->all()['cache_minutes']);
        update_option('drf_api_status', ['checked_at' => time(), 'breed_count' => count($breeds)], false);
        return $breeds;
    }
}
