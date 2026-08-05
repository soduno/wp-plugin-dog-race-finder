<?php

declare(strict_types=1);
namespace DogRaceFinder\Admin;

use DogRaceFinder\Cache\BreedCache;
use DogRaceFinder\Settings\{SettingsRepository, SettingsValidator};

final class SettingsRegistration
{
    public const GROUP = 'drf_settings_group';
    public const PAGE = 'dog-race-finder-settings';

    public function __construct(private SettingsRepository $settings, private SettingsValidator $validator, private BreedCache $cache, private View $view) {}

    public function register(): void
    {
        add_action('admin_init', [$this, 'registerFields']);
    }

    public function registerFields(): void
    {
        register_setting(self::GROUP, SettingsRepository::OPTION, [
            'type' => 'array',
            'sanitize_callback' => [$this->validator, 'sanitize'],
            'default' => SettingsRepository::DEFAULTS,
            'show_in_rest' => false,
        ]);
        add_settings_section('gallery', 'Galleri og favoritter', [$this, 'renderIntroduction'], self::PAGE);
        foreach ([
            'image_count' => ['Antal billeder', 'number', 1, 50],
            'columns' => ['Antal kolonner', 'number', 1, 6],
            'default_breed' => ['Standardrace', 'select'],
            'allowed_breeds' => ['Tilladte racer', 'multiselect'],
            'favorites_enabled' => ['Favoritter', 'checkbox'],
            'cache_minutes' => ['Cachevarighed (minutter)', 'number', 1, 1440],
        ] as $key => $field) {
            add_settings_field($key, $field[0], [$this, 'renderField'], self::PAGE, 'gallery', [
                'label_for' => 'drf_' . $key,
                'key' => $key,
                'type' => $field[1],
                'min' => $field[2] ?? null,
                'max' => $field[3] ?? null,
            ]);
        }
    }

    public function renderIntroduction(): void
    {
        echo '<p>Indstillingerne gemmes i WordPress-databasen. De bruges af galleriet og de separate frontendkomponenter. Dog CEO kræver ingen API-nøgle.</p>';
    }

    public function renderField(array $args): void
    {
        $this->view->render('field', ['field' => $args, 'settings' => $this->settings->all(), 'breeds' => $this->cache->get()]);
    }
}
