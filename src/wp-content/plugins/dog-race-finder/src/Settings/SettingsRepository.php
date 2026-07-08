<?php

declare(strict_types=1);
namespace DogRaceFinder\Settings;

final class SettingsRepository
{
    public const OPTION = 'drf_settings';
    public const DEFAULTS = [
        'image_count' => 12,
        'columns' => 3,
        'default_breed' => '',
        'allowed_breeds' => [],
        'favorites_enabled' => true,
        'cache_minutes' => 60,
    ];

    public function all(): array
    {
        $stored = get_option(self::OPTION, []);
        return array_replace(self::DEFAULTS, is_array($stored) ? array_intersect_key($stored, self::DEFAULTS) : []);
    }
}
