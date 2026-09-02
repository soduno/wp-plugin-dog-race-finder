<?php if (!defined('ABSPATH')) { exit; } ?>
<section class="drf drf-results" data-drf-component="results" data-group="<?php echo esc_attr($data['group']); ?>" data-endpoint="<?php echo esc_url($data['endpoint']); ?>" data-breed="<?php echo esc_attr($data['breed']); ?>" data-count="<?php echo esc_attr((string) $data['count']); ?>" data-favorites="<?php echo $data['settings']['favorites_enabled'] ? '1' : '0'; ?>" style="--drf-columns:<?php echo (int) $data['columns']; ?>" aria-label="Hunderesultater">
    <div class="drf-results-heading"><h3 data-drf-heading>Et godt sted at starte</h3><button type="button" class="drf-text-button" data-drf-refresh>Vis nye hunde ↻</button></div>
    <p class="drf-status" role="status" aria-live="polite"></p>
    <div class="drf-grid" data-drf-grid></div>
    <div class="drf-more"><button type="button" class="drf-button drf-button-secondary" data-drf-more>Vis flere hunde</button></div>
    <noscript>Aktivér JavaScript for at se hundegalleriet.</noscript>
</section>
