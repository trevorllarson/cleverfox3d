<?php
get_header();
while ( have_posts() ) :
  the_post();
  ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="page-header">
      <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </header>
    <div class="page-content">
      <?php the_content(); ?>
    </div>
  </article>
  <?php
endwhile;
get_footer();
