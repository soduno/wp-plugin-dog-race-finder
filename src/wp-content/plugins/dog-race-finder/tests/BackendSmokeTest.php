<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/template.php';

use DogRaceFinder\Cache\BreedCache;
use DogRaceFinder\Cache\ImageCache;
use DogRaceFinder\Frontend\Components;
use DogRaceFinder\Settings\SettingsRepository;
use DogRaceFinder\Settings\SettingsValidator;

final class BackendSmokeTest
{
    private int $requests = 0;
    private bool $fail = false;

    public function response(mixed $response, array $arguments, string $url): mixed
    {
        if (!str_starts_with($url, \DogRaceFinder\API_BASE_URL)) { return $response; }
        $this->requests++;
        if ($this->fail) { return new WP_Error('test_timeout', 'Simulated timeout'); }
        return [
            'response' => ['code' => 200],
            'headers' => [],
            'body' => wp_json_encode(['status' => 'success', 'message' => [
                \DogRaceFinder\IMAGE_BASE_URL . 'breeds/hound-afghan/test.jpg',
                \DogRaceFinder\IMAGE_BASE_URL . 'breeds/pug/test.jpg',
                'https://untrusted.example.test/breeds/pug/test.jpg',
            ]]),
        ];
    }

    public function run(): void
    {
        $settings = new SettingsRepository();
        $cache = new BreedCache();
        $oldSettings = get_option(SettingsRepository::OPTION, null);
        $oldCatalogue = $cache->get();
        $catalogue = ['hound' => 'Hound', 'hound/afghan' => 'Afghan Hound', 'pug' => 'Pug'];
        $cache->put($catalogue, 60);
        add_filter('pre_http_request', [$this, 'response'], 10, 3);
        try {
            $server = rest_get_server();
            $this->request($server, ['breed' => '../etc'], 400);
            $this->request($server, ['count' => 51], 400);
            $this->request($server, ['count' => 0], 400);
            $this->request($server, ['breed' => 'nonexistent'], 400);
            $validator = new SettingsValidator($settings, $cache);
            $valid = $validator->sanitize(array_replace(SettingsRepository::DEFAULTS, ['favorites_enabled' => '1']));
            $this->assert($validator->sanitize($valid) === $valid, 'Settings sanitizer is idempotent');
            $invalid = $validator->sanitize(['image_count' => -1, 'columns' => 99, 'cache_minutes' => 0]);
            $this->assert($invalid['image_count'] === $settings->all()['image_count'], 'Invalid settings preserve previous values');
            update_option(SettingsRepository::OPTION, array_replace(SettingsRepository::DEFAULTS, ['allowed_breeds' => ['hound']]));
            (new ImageCache())->clear();
            $images = $this->request($server, ['breed' => 'hound', 'count' => 5], 200)['images'];
            $this->assert(count($images) === 1 && $images[0]['breed'] === 'hound/afghan', 'Parent race allows child images and rejects unapproved images');
            $before = $this->requests;
            $this->request($server, ['breed' => 'hound', 'count' => 5], 200);
            $this->assert($this->requests === $before, 'Image requests use cached pool');
            $this->request($server, ['breed' => 'pug'], 400);
            $this->fail = true;
            (new ImageCache())->clear();
            $this->request($server, ['breed' => 'hound'], 502);
            $components = new Components();
            foreach (['gallery', 'search', 'results', 'favoritesButton', 'favorites'] as $method) {
                $this->assert(str_contains($components->$method(['group' => 'test']), 'data-group="test"'), 'PHP component ' . $method);
            }
            $this->assert($components->heart(['url' => 'javascript:alert(1)']) === '', 'Unsafe standalone heart URL rejected');
            update_option(SettingsRepository::OPTION, array_replace(SettingsRepository::DEFAULTS, ['favorites_enabled' => false]));
            $this->assert($components->favoritesButton() === '' && $components->favorites() === '', 'Disabled favorites hide standalone components');
            $this->assert(!str_contains($components->results(), 'data-favorites="1"'), 'Disabled favorites remove result hearts');
            $this->assert(str_contains(do_shortcode('[drf_search group="one"]'), 'data-group="one"'), 'Registered shortcode renders independently');
            echo "All PHP integration checks passed.\n";
        } finally {
            remove_filter('pre_http_request', [$this, 'response'], 10);
            if ($oldSettings === null) { delete_option(SettingsRepository::OPTION); } else { update_option(SettingsRepository::OPTION, $oldSettings); }
            if ($oldCatalogue === null) { $cache->clear(); } else { $cache->put($oldCatalogue, 60); }
            (new ImageCache())->clear();
        }
    }

    private function request(WP_REST_Server $server, array $parameters, int $expected): array
    {
        $request = new WP_REST_Request('GET', '/dog-race-finder/v1/images');
        $request->set_query_params($parameters);
        $response = $server->dispatch($request);
        $this->assert($response->get_status() === $expected, 'REST status ' . $expected . ' for ' . wp_json_encode($parameters));
        return $response->get_data();
    }

    private function assert(bool $condition, string $message): void
    {
        if (!$condition) { throw new RuntimeException($message); }
        echo 'PASS: ' . $message . "\n";
    }
}

(new BackendSmokeTest())->run();