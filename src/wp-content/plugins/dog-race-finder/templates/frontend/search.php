<?php if (!defined('ABSPATH')) { exit; } $id = wp_unique_id('drf-search-'); ?>
<form class="drf drf-search" data-drf-component="search" data-group="<?php echo esc_attr($data['group']); ?>" data-endpoint="<?php echo esc_url($data['endpoint']); ?>" data-breed="<?php echo esc_attr($data['breed']); ?>">
    <div class="drf-search-fields">
        <label for="<?php echo esc_attr($id); ?>">Søg efter race<input type="search" id="<?php echo esc_attr($id); ?>" placeholder="Fx Labrador eller terrier" autocomplete="off" data-drf-query></label>
        <label for="<?php echo esc_attr($id . '-breed'); ?>">Vælg race<select id="<?php echo esc_attr($id . '-breed'); ?>" data-drf-breed disabled><option value="">Indlæser racer…</option></select></label>
    </div>
    <button type="submit" class="drf-button" disabled>Find hunde <span aria-hidden="true">↗</span></button>
    <p class="drf-search-status" role="status"></p>
    <noscript>Aktivér JavaScript for at søge efter hunde.</noscript>
</form>
