<?php

declare(strict_types=1);
namespace DogRaceFinder\Frontend;

final class Assets
{
    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
        add_filter('script_loader_tag', [$this, 'moduleTag'], 10, 2);
    }

    public function enqueue(): void
    {
        // Enqueue early so shortcodes in widgets and theme templates work after wp_head too.
        $file = \DogRaceFinder\PLUGIN_FILE;
        $base = plugins_url('assets/', $file);
        wp_enqueue_style('drf-gallery', $base . 'css/gallery.css', [], (string) filemtime(dirname($file) . '/assets/css/gallery.css'));
        wp_enqueue_script('drf-gallery', $base . 'js/gallery.js', [], (string) filemtime(dirname($file) . '/assets/js/gallery.js'), true);
        wp_add_inline_script('drf-gallery', 'window.drfConfig = ' . wp_json_encode(['imageBaseUrl' => \DogRaceFinder\IMAGE_BASE_URL]) . ';', 'before');
    }

    public function moduleTag(string $tag, string $handle): string
    {
        if ($handle !== 'drf-gallery') { return $tag; }
        return preg_replace('/<script\b[^>]*\bsrc=/', '<script type="module" src=', $tag) ?? $tag;
    }
}
