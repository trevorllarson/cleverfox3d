<?php
get_header();
if ( have_posts() ):
  while ( have_posts() ) :
    the_post();
    get_template_part( 'template-parts/content', get_post_type() );
  endwhile;
  the_posts_navigation();
  /*
  <!-- custom pagination setup -->
  <div class="container pad-sm">
      <div class="row justify-content-center">
          <div class="col">
              <nav aria-label="Resource Pagination">
                  <ul class="pagination justify-content-center">
                      <li class="item-paging page-item page-prev <?php echo (!get_previous_post_link()) ? 'disabled' : '' ?>">
                          <a class="page-link" href="<?php echo get_the_permalink(get_adjacent_post(false, '', true, 'category')); ?>" tabindex="-1">Previous</span></a>
                      </li>
                      <li class="item-paging page-item page-next <?php echo (!get_next_post_link()) ? 'disabled' : '' ?>">
                          <a class="page-link" href="<?php echo get_the_permalink(get_adjacent_post(false, '', false, 'category')); ?>">Next</span></a>
                      </li>
                  </ul>
              </nav>
              </nav>
          </div>
      </div>
  </div>
  */
else:
  get_template_part( 'template-parts/content', 'none' );
endif;
get_footer();
