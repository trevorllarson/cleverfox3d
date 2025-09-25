<?php
get_header();
if( have_posts() ):
  ?>
  <header class="page-header">
    <h1 class="page-title">
      <?php printf( esc_html__( 'Search Results for: %s', 'electricpulp' ), '<span>' . get_search_query() . '</span>' ); ?>
    </h1>
  </header>
  <?php
  while(have_posts()):
    the_post();
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <header class="entry-header">
        <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
        <?php if ( 'post' === get_post_type() ) : ?>
          <div class="entry-meta">
              <?php electricpulp_posted_on(); ?>
              <?php electricpulp_posted_by(); ?>
          </div>
        <?php endif; ?>
      </header>
      <?php electricpulp_post_thumbnail(); ?>
      <div class="entry-summary">
        <?php the_excerpt(); ?>
      </div>
      <footer class="entry-footer">
        <?php electricpulp_entry_footer(); ?>
      </footer>
    </article>
    <?php
  endwhile;
  the_posts_navigation();
else:
  get_template_part( 'template-parts/content', 'none' );
endif;
get_footer();
