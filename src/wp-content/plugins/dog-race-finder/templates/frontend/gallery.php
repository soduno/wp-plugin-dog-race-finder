<?php if (!defined('ABSPATH')) { exit; } ?>
<section class="drf-shell" aria-label="Find din yndlingshund">
    <header class="drf-intro"><span class="drf-eyebrow">SMÅ POTER. STORE PERSONLIGHEDER.</span><h2>Find din yndlingshund<span aria-hidden="true">.</span></h2><p>Udforsk hunderacer, find nye favoritter, og gem dem, du falder for.</p></header>
    <div class="drf-toolbar">
        <?php echo $this->render('search', $data); ?>
        <?php echo $this->render('favorites_button', $data); ?>
    </div>
    <?php echo $this->render('results', $data); ?>
    <footer class="drf-credit">Billeder fra <a href="<?php echo esc_url(\DogRaceFinder\API_WEBSITE_URL); ?>" target="_blank" rel="noopener noreferrer">Dog CEO</a><?php if ($data["settings"]["favorites_enabled"]): ?> · Favoritter gemmes i denne browser.<?php endif; ?></footer>
</section>
