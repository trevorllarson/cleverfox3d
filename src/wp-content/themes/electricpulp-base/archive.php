<?php
get_header();
if( have_posts() ):
  ?>
  <header class="page-header">
    <?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
    <?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
  </header>
  <?php
  while( have_posts() ):
    the_post();
    get_template_part( 'template-parts/content', get_post_type() );
  endwhile;
  the_posts_navigation();
else:
  get_template_part( 'template-parts/content', 'none' );
endif;
get_footer();
