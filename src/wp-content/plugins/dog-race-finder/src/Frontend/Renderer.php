<?php

declare(strict_types=1);
namespace DogRaceFinder\Frontend;

use DogRaceFinder\Settings\SettingsRepository;

final class Renderer
{
    public function __construct(private SettingsRepository $settings) {}

    public function render(string $component, array $attributes = []): string
    {
        if (!in_array($component, ['gallery', 'search', 'favorites_button', 'favorites', 'results', 'heart'], true)) {
            return '';
        }
        $settings = $this->settings->all();
        if (in_array($component, ['favorites_button', 'favorites', 'heart'], true) && !$settings['favorites_enabled']) {
            return '';
        }
        $group = sanitize_key((string) ($attributes['group'] ?? 'dogs')) ?: 'dogs';
        $data = [
            'group' => $group,
            'settings' => $settings,
            'endpoint' => rest_url('dog-race-finder/v1/'),
            'breed' => preg_match('~^([a-z]+(/[a-z]+)?)?$~D', (string) ($attributes['breed'] ?? '')) ? (string) ($attributes['breed'] ?? $settings['default_breed']) : (string) $settings['default_breed'],
            'count' => min(50, max(1, (int) ($attributes['count'] ?? $settings['image_count']))),
            'columns' => min(6, max(1, (int) ($attributes['columns'] ?? $settings['columns']))),
            'url' => (string) ($attributes['url'] ?? ''),
            'label' => sanitize_text_field((string) ($attributes['label'] ?? 'Hund')),
        ];
        if ($component === 'heart' && !preg_match('~^' . preg_quote(\DogRaceFinder\IMAGE_BASE_URL, '~') . 'breeds/[a-z-]+/[^\s]+$~D', $data['url'])) {
            return '';
        }
        ob_start();
        require dirname(__DIR__, 2) . '/templates/frontend/' . $component . '.php';
        return (string) ob_get_clean();
    }
}
