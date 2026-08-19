<?php

declare(strict_types=1);
namespace DogRaceFinder\Rest;

use DogRaceFinder\Frontend\{Catalogue, ImageService};
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

final class GalleryController
{
    public function __construct(private Catalogue $catalogue, private ImageService $images) {}

    public function register(): void
    {
        add_action('rest_api_init', [$this, 'routes']);
    }

    public function routes(): void
    {
        // Deliberately public: read-only gallery data, no credentials or private user data.
        register_rest_route('dog-race-finder/v1', '/breeds', [
            'methods' => 'GET', 'permission_callback' => '__return_true', 'callback' => [$this, 'breeds'],
        ]);
        register_rest_route('dog-race-finder/v1', '/images', [
            'methods' => 'GET', 'permission_callback' => '__return_true', 'callback' => [$this, 'images'],
            'args' => [
                'breed' => ['type' => 'string', 'default' => '', 'pattern' => '^([a-z]+(/[a-z]+)?)?$'],
                'count' => ['type' => 'integer', 'default' => 12, 'minimum' => 1, 'maximum' => 50],
            ],
        ]);
    }

    public function breeds(): WP_REST_Response|WP_Error
    {
        $breeds = $this->catalogue->breeds();
        return is_wp_error($breeds) ? $breeds : new WP_REST_Response(['breeds' => $breeds]);
    }

    public function images(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $images = $this->images->find((string) $request['breed'], (int) $request['count']);
        return is_wp_error($images) ? $images : new WP_REST_Response(['images' => $images]);
    }
}
