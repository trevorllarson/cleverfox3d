<?php

use Pulp\Template;

get_header();
while (have_posts()) {
    the_post();
    $hasHeader = 'no-page-header';
    if (! Template::hasHeading1()) {
        get_template_part('parts/element/page-header', null, ['title' => get_the_title()]);
        $hasHeader = '';
    }
    ?>
    <div class="page-content <?php echo $hasHeader ?>">
        <?php the_content(); ?>
    </div>
    <?php
}
get_footer();
