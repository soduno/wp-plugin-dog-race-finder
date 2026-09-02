<?php if (!defined('ABSPATH')) { exit; } ?>
<section class="drf drf-saved" data-drf-component="favorites" data-group="<?php echo esc_attr($data['group']); ?>" style="--drf-columns:<?php echo (int) $data['columns']; ?>" aria-label="Gemte hundebilleder">
    <h3>Mine favoritter</h3><p class="drf-status" role="status" aria-live="polite"></p><div class="drf-grid" data-drf-grid></div>
    <noscript>Aktivér JavaScript for at se gemte billeder.</noscript>
</section>
