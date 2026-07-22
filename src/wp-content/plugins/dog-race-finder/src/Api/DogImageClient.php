<?php

declare(strict_types=1);
namespace DogRaceFinder\Api;

use WP_Error;

/** Fetches images only from a fixed, trusted provider. */
final class DogImageClient
{
    public function fetch(string $breed): array|WP_Error
    {
        $path = $breed === '' ? 'breeds/image/random/50' : 'breed/' . $breed . '/images/random/50';
        $response = wp_remote_get(\DogRaceFinder\API_BASE_URL . $path, [
            'timeout' => 10, 'redirection' => 0, 'limit_response_size' => 262144,
        ]);
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return new WP_Error('drf_images', 'Billederne kunne ikke hentes. Prøv igen om lidt.', ['status' => 502]);
        }
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($data) || ($data['status'] ?? '') !== 'success' || !is_array($data['message'] ?? null)) {
            return new WP_Error('drf_images', 'Billedtjenesten returnerede et ugyldigt svar.', ['status' => 502]);
        }
        $images = [];
        foreach ($data['message'] as $url) {
            if (!is_string($url) || !preg_match('~^' . preg_quote(\DogRaceFinder\IMAGE_BASE_URL, '~') . 'breeds/([a-z-]+)/[^\s]+$~D', $url, $matches)) {
                continue;
            }
            $segments = explode('-', $matches[1], 2);
            $slug = implode('/', $segments);
            $images[$url] = ['url' => esc_url_raw($url), 'breed' => $slug, 'label' => ucwords(implode(' ', array_reverse($segments)))];
        }
        return array_values($images);
    }
}
