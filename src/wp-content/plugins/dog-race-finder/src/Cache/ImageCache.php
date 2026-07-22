<?php

declare(strict_types=1);
namespace DogRaceFinder\Cache;

final class ImageCache
{
    private function key(string $breed): string
    {
        return 'drf_images_' . get_option('drf_image_cache_version', '1') . '_' . md5($breed);
    }

    public function get(string $breed): ?array
    {
        $value = get_transient($this->key($breed));
        return is_array($value) ? $value : null;
    }

    public function put(string $breed, array $images): void
    {
        set_transient($this->key($breed), $images, MINUTE_IN_SECONDS);
    }

    public function clear(): void
    {
        // Invalidate all breed pools; old entries expire after at most one minute.
        update_option('drf_image_cache_version', wp_generate_uuid4(), false);
    }
}
