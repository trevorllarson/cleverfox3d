<?php
// Default class if not provided
if (empty($class)) {
    $class = 'button';
} ?>
<a
    href="<?= esc_url($link['url']); ?>"
    class="<?= esc_attr($class); ?>"
    <?php if (!empty($link['target'])): ?>
        target="<?= esc_attr($link['target']); ?>" rel="noopener noreferrer"
    <?php endif; ?>>
    <?= esc_html($link['title']); ?>
</a>