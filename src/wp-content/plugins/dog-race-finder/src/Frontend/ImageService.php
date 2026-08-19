<?php

declare(strict_types=1);
namespace DogRaceFinder\Frontend;

use DogRaceFinder\Api\DogImageClient;
use DogRaceFinder\Cache\ImageCache;
use DogRaceFinder\Settings\SettingsRepository;
use WP_Error;

final class ImageService
{
    public function __construct(private Catalogue $catalogue, private DogImageClient $client, private ImageCache $cache, private SettingsRepository $settings) {}

    public function find(string $breed, int $count): array|WP_Error
    {
        $breeds = $this->catalogue->breeds();
        if (is_wp_error($breeds)) {
            return $breeds;
        }
        if ($breed !== '' && !isset($breeds[$breed])) {
            return new WP_Error('drf_breed', 'Denne race er ikke tilgængelig.', ['status' => 400]);
        }
        if ($breeds === []) {
            return [];
        }
        $settings = $this->settings->all();
        if ($breed === '' && $settings['allowed_breeds'] !== []) {
            $breed = (string) array_rand($breeds);
        }
        $images = $this->cache->get($breed);
        if ($images === null) {
            $images = $this->client->fetch($breed);
            if (is_wp_error($images)) {
                return $images;
            }
            $this->cache->put($breed, $images);
        }
        if ($settings['allowed_breeds'] !== []) {
            $images = array_values(array_filter($images, static function (array $image) use ($breeds): bool {
                foreach (array_keys($breeds) as $allowed) {
                    if ($image['breed'] === $allowed || str_starts_with($image['breed'], $allowed . '/')) {
                        return true;
                    }
                }
                return false;
            }));
        }
        shuffle($images);
        return array_slice($images, 0, min(50, max(1, $count)));
    }
}
