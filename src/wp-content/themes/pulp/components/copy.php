<?php if ($hasWrapper): ?>
    <<?= esc_html($wrapperEl); ?> class="<?= esc_attr($wrapperClass); ?>">
<?php endif; ?>
    <?= wp_kses($content, [
        'p' => [],
        'div' => [],
        'br' => [],
        'strong' => [],
        'em' => [],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'a' => [
            'href' => true,
            'title' => true,
            'target' => true,
            'rel' => true,
        ],
        'iframe' => [
            'src' => true,
            'width' => true,
            'height' => true,
            'frameborder' => true,
            'allow' => true,
            'allowfullscreen' => true,
            'title' => true,
        ],
        'span' => [
            'class' => true,
        ],
    ]); ?>
<?php if ($hasWrapper): ?>
    </<?= esc_html($wrapperEl); ?>>
<?php endif; ?>