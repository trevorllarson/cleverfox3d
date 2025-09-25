<div class="video-responsive">
    <?= wp_kses($content, [
        'iframe' => [
            'src' => true,
            'width' => true,
            'height' => true,
            'frameborder' => true,
            'allow' => true,
            'allowfullscreen' => true,
            'title' => true
        ],
    ]); ?>
</div>
