<?php
if (!defined('ABSPATH')) { exit; }
$status = $data['status'];
?>
<div class="wrap">
    <h1>Dog Race Finder</h1>
    <p>Administrér racevalg, galleriindstillinger og forbindelsen til Dog CEO.</p>
    <?php if (is_array($data['notice'])): ?>
        <div class="notice <?php echo $data['notice']['error'] ? 'notice-error' : 'notice-success'; ?> is-dismissible"><p><?php echo esc_html($data['notice']['message']); ?></p></div>
    <?php endif; ?>
    <h2>API og cache</h2>
    <table class="widefat striped" style="max-width:850px"><tbody>
        <tr><th scope="row">Datakilde</th><td><a href="<?php echo esc_url(\DogRaceFinder\API_WEBSITE_URL); ?>" target="_blank" rel="noopener noreferrer">Dog CEO</a> – ingen API-nøgle nødvendig.</td></tr>
        <tr><th scope="row">Seneste vellykkede API-test</th><td><?php echo esc_html(!empty($status['checked_at']) ? wp_date('d/m/Y H:i:s', (int) $status['checked_at']) : 'Endnu ikke testet'); ?></td></tr>
        <tr><th scope="row">Racecache</th><td><?php echo esc_html($data['breeds'] === null ? 'Tom eller udløbet' : count($data['breeds']) . ' racer og underracer'); ?></td></tr>
        <tr><th scope="row">Cachevarighed</th><td><?php echo esc_html((string) $data['settings']['cache_minutes']); ?> minutter</td></tr>
    </tbody></table>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="drf_refresh_breeds">
        <?php wp_nonce_field('drf_refresh_breeds'); submit_button('Test API-forbindelse og hent racer', 'primary'); ?>
    </form>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="drf_clear_cache">
        <?php wp_nonce_field('drf_clear_cache'); submit_button('Ryd cache', 'secondary'); ?>
    </form>
    <h2>Kom i gang</h2>
    <p>Hent først racelisten, og vælg derefter dine <a href="<?php echo esc_url(admin_url('admin.php?page=dog-race-finder-settings')); ?>">indstillinger</a>.</p>
    <p>Indsæt shortcodes på en side, eller brug PHP-komponenterne direkte i dit tema. Komponenter med samme group arbejder sammen.</p>
<h2>Frontendkomponenter</h2>
<table class="widefat striped" style="max-width:850px"><thead><tr><th>Komponent</th><th>Shortcode</th></tr></thead><tbody>
<?php foreach (['Samlet galleri' => '[dog_race_finder group="dogs"]', 'Søgefelt og racevalg' => '[drf_search group="dogs"]', 'Favoritknap med tæller' => '[drf_favorites_button group="dogs"]', 'Søgeresultater' => '[drf_results group="dogs"]', 'Gemte billeder' => '[drf_favorites]', 'Enkelt hjerte' => '[drf_heart url="BILLEDETS_URL_FRA_API" label="Hund"]'] as $label => $shortcode): ?>
<tr><td><?php echo esc_html($label); ?></td><td><code><?php echo esc_html($shortcode); ?></code></td></tr>
<?php endforeach; ?>
</tbody></table>
<p>PHP: <code>$dogs = new \DogRaceFinder\Frontend\Components(); echo $dogs->search(['group' => 'dogs']);</code></p>
</div>
