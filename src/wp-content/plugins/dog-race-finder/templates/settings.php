<?php
/** @var array $data */
if (!defined('ABSPATH')) { exit; }
?>
<div class="wrap">
    <h1>Dog Race Finder – Indstillinger</h1>
    <?php settings_errors(); ?>
    <form action="<?php echo esc_url(admin_url('options.php')); ?>" method="post">
        <?php
        settings_fields(\DogRaceFinder\Admin\SettingsRegistration::GROUP);
        do_settings_sections(\DogRaceFinder\Admin\SettingsRegistration::PAGE);
        submit_button('Gem indstillinger');
        ?>
    </form>
</div>
