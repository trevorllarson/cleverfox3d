<?php
$title = esc_html(get_the_title());
if ($word = get_field('highlighted_page_title_word')) {
    $title = str_replace($word, '<strong>' . $word . '</strong>', $title);
}
$classes = ['page-header'];
if (get_field('page_header_height') !== 'default') {
    $classes[] = 'is-style-' . get_field('page_header_height');
}
?>
<header class="<?php echo implode(' ', $classes) ?>">
    <h1><?php echo $title ?></h1>
    <?php if ($value = get_field('subtitle')) { ?>
        <p class="subtitle"><?php echo esc_html($value) ?></p>
    <?php } ?>
</header>
