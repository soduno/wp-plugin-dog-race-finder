<?php
if (!defined('ABSPATH')) { exit; }
$field = $data['field'];
$key = $field['key'];
$value = $data['settings'][$key];
$name = \DogRaceFinder\Settings\SettingsRepository::OPTION . '[' . $key . ']';
$id = 'drf_' . $key;
$breeds = $data['breeds'];
?>
<?php if ($field['type'] === 'number'): ?>
    <input type="number" class="small-text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr((string) $value); ?>" min="<?php echo esc_attr((string) $field['min']); ?>" max="<?php echo esc_attr((string) $field['max']); ?>" required>
    <?php if ($key === 'cache_minutes'): ?><p class="description">Gælder næste gang racelisten hentes. Fra 1 minut til 24 timer.</p><?php endif; ?>
<?php elseif ($field['type'] === 'checkbox'): ?>
    <label><input type="checkbox" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="1" <?php checked((bool) $value); ?>> Tillad besøgende at gemme favoritter</label>
<?php else: ?>
    <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name . ($field['type'] === 'multiselect' ? '[]' : '')); ?>" <?php if ($field['type'] === 'multiselect') { echo 'multiple size="10"'; } ?> <?php disabled($breeds === null); ?>>
        <?php if ($field['type'] === 'select'): ?><option value="">Alle racer</option><?php endif; ?>
        <?php foreach ($breeds ?? [] as $breed => $label): ?>
            <option value="<?php echo esc_attr($breed); ?>" <?php selected(is_array($value) ? in_array($breed, $value, true) : $value === $breed); ?>><?php echo esc_html($label); ?></option>
        <?php endforeach; ?>
    </select>
    <p class="description"><?php echo esc_html($breeds === null ? 'Hent racelisten via Test API-forbindelse på oversigten. Eksisterende racevalg bevares, indtil listen er hentet igen.' : ($field['type'] === 'multiselect' ? 'Ingen valgte betyder alle racer. Hold Ctrl (Windows) eller Cmd (Mac) for at vælge flere eller fjerne valg.' : 'Vælg den race, galleriet starter med.')); ?></p>
<?php endif; ?>
