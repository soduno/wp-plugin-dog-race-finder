<?php
/**
 * Plugin Name: Dog Race Finder
 * Description: Modulært hundegalleri med racefilter, favoritter, shortcodes og backendindstillinger.
 * Version: 1.0.0
 * Author: simonduun
 * Author URI: https://simonduun.com
 * Requires at least: 6.6
 * Requires PHP: 8.3
 * Text Domain: dog-race-finder
 */

declare(strict_types=1);

namespace DogRaceFinder;

if (!defined('ABSPATH')) {
    exit;
}

// External service configuration. WordPress and asset URLs are resolved dynamically.
const API_BASE_URL = 'https://dog.ceo/api/';
const IMAGE_BASE_URL = 'https://images.dog.ceo/';
const API_WEBSITE_URL = 'https://dog.ceo/dog-api/';
const PLUGIN_FILE = __FILE__;

require_once __DIR__ . '/vendor/autoload.php';

(new Bootstrap())->boot();
