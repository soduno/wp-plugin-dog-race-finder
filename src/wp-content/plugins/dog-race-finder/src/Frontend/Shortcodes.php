<?php

declare(strict_types=1);
namespace DogRaceFinder\Frontend;

final class Shortcodes
{
    public function __construct(private Renderer $renderer) {}

    public function register(): void
    {
        add_action('init', [$this, 'add']);
    }

    public function add(): void
    {
        foreach (['dog_race_finder' => 'gallery', 'drf_search' => 'search', 'drf_favorites_button' => 'favorites_button', 'drf_favorites' => 'favorites', 'drf_results' => 'results', 'drf_heart' => 'heart'] as $tag => $component) {
            add_shortcode($tag, fn ($attributes): string => $this->renderer->render($component, is_array($attributes) ? $attributes : []));
        }
    }
}
