<?php

declare(strict_types=1);
namespace DogRaceFinder\Settings;

use DogRaceFinder\Cache\BreedCache;

final class SettingsValidator
{
    public function __construct(private SettingsRepository $settings, private BreedCache $cache) {}

    public function sanitize(mixed $input): array
    {
        $old = $this->settings->all();
        if (!is_array($input)) {
            add_settings_error(SettingsRepository::OPTION, 'invalid', 'Indstillingerne kunne ikke læses.');
            return $old;
        }
        $result = $old;
        foreach (['image_count' => [1, 50], 'columns' => [1, 6], 'cache_minutes' => [1, 1440]] as $key => [$min, $max]) {
            $value = filter_var($input[$key] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]);
            if ($value === false) {
                add_settings_error(SettingsRepository::OPTION, $key, sprintf('Feltet %s skal være et heltal mellem %d og %d.', $key, $min, $max));
            } else {
                $result[$key] = $value;
            }
        }
        $result['favorites_enabled'] = in_array($input['favorites_enabled'] ?? false, [true, 1, '1'], true);
        $breeds = $this->cache->get();
        // Preserve selections if no validated API catalogue is available.
        if (is_array($breeds)) {
            $allowed = $input['allowed_breeds'] ?? [];
            if (!is_array($allowed) || array_filter($allowed, static fn ($breed): bool => !is_string($breed) || !array_key_exists($breed, $breeds))) {
                add_settings_error(SettingsRepository::OPTION, 'allowed_breeds', 'Vælg kun racer fra listen.');
            } else {
                $result['allowed_breeds'] = array_values(array_unique($allowed));
            }
            $default = $input['default_breed'] ?? '';
            if (!is_string($default) || ($default !== '' && !array_key_exists($default, $breeds))) {
                add_settings_error(SettingsRepository::OPTION, 'default_breed', 'Standardracen findes ikke i racelisten.');
            } else {
                $result['default_breed'] = $default;
            }
        }
        if ($result['default_breed'] !== '' && $result['allowed_breeds'] !== [] && !in_array($result['default_breed'], $result['allowed_breeds'], true)) {
            add_settings_error(SettingsRepository::OPTION, 'breed_conflict', 'Standardracen skal være blandt de tilladte racer. Standardracen er nulstillet.', 'warning');
            $result['default_breed'] = '';
        }
        return $result;
    }
}
