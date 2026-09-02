<?php

declare(strict_types=1);
namespace DogRaceFinder\Frontend;

use DogRaceFinder\Settings\SettingsRepository;

/** Public PHP entry point for theme templates: echo (new Components())->search([...]); */
final class Components
{
    private Renderer $renderer;

    public function __construct(?Renderer $renderer = null)
    {
        $this->renderer = $renderer ?? new Renderer(new SettingsRepository());
    }

    public function gallery(array $attributes = []): string { return $this->renderer->render('gallery', $attributes); }
    public function search(array $attributes = []): string { return $this->renderer->render('search', $attributes); }
    public function favoritesButton(array $attributes = []): string { return $this->renderer->render('favorites_button', $attributes); }
    public function favorites(array $attributes = []): string { return $this->renderer->render('favorites', $attributes); }
    public function results(array $attributes = []): string { return $this->renderer->render('results', $attributes); }
    public function heart(array $attributes = []): string { return $this->renderer->render('heart', $attributes); }
}
