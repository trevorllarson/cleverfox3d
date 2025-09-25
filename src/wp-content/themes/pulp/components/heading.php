<?php if ($hasWrapper): ?>
    <div class="<?= esc_attr($wrapperClass); ?>">
<?php endif; ?>

    <<?= esc_html($size); ?> class="<?= esc_attr($class); ?>">
        <?= wp_kses_post($content); ?>
    </<?= esc_html($size); ?>>

<?php if ($hasWrapper): ?>
    </div>
<?php endif; ?>