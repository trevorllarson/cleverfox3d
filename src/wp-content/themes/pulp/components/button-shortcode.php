<a
    href="<?= esc_url($href); ?>"
    class="btn <?= esc_attr($buttonClass); ?>"
    <?= !empty($target) ? 'target="' . esc_attr($target) . '" rel="noopener noreferrer"' : ''; ?>
    data-content="<?= esc_attr($content); ?>">
    <?= esc_html($content); ?>
</a>
