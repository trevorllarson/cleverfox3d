<?php

use Pulp\Template;

get_header();
while (have_posts()) {
    the_post();
    ?>
    <header class="page-header">
        <div class="container">
            <h1><?php echo esc_html(get_the_title()); ?></h1>
        </div>
    </header>

    <div class="container">
        <article class="article-layout">
            <div class="entry-content">

                <?php if (has_post_thumbnail()) { ?>
                    <div class="featured-image"><?php echo Template::getGlideImageTag(get_post_thumbnail_id(), ["fit" => "crop","w" => 690, "h" => 461], 'eager') ?></div>
                <?php } ?>

                <?php the_content(); ?>

            </div>
           
        </article>
    </div>
    <?php
}

get_footer();
