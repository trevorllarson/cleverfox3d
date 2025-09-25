<?php

get_header();
if (have_posts()) {
    ?>
    <header class="archive-header">
        <div class="container">
            <h1><?php echo esc_html(get_the_title(get_option('page_for_posts'))) ?>
            <?php
            if (get_query_var('paged')) {
                ?>
                 &mdash; Page <?php echo get_query_var('paged') ?>
                <?php
            }
            ?>
            </h1>
        </div>
    </header>
    <div class="container">
        <div class="article-grid">
            <?php
            while (have_posts()) {
                the_post();
                get_template_part('parts/post/loop-item');
            }
            ?>
        </div>
        <?php get_template_part('parts/element/load-more'); ?>

    </div>
    <?php
} else {
    get_template_part('parts/element/not-found', null, ['text' => 'Sorry, no articles found for this topic.']);
}
 get_footer();
