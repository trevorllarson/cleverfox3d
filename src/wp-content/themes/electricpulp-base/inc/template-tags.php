<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package EP_Base
 */

function electricpulp_favicons(){
  // 11-3-21: Updated to match current output from https://www.favicon-generator.org/
  // https://sympli.io/blog/2017/02/15/heres-everything-you-need-to-know-about-favicons-in-2017/
  // Googles recommendations: https://developers.google.com/web/fundamentals/design-and-ux/browser-customization/
  $standardSizes = array(
    16,
    32,
    96,
  );
  $touchIcons = array(
    57,
    60,
    72,
    76,
    114,
    120,
    144,
    152,
    180,
  );
  $androidIcons = array(
    36,
    48,
    72,
    96,
    114,
    192
  );
  // MS tiles: https://docs.microsoft.com/en-us/previous-versions/windows/internet-explorer/ie-developer/samples/dn455106%28v%3dvs.85%29
  $msTiles = array(
    70,
    144,
    150,
    310
  );
  $themeColor = '#ffffff';
  $iconPath = '/assets/images/favicons/';
  ?>
  <?php foreach ($standardSizes as $size) { ?>
    <link rel="icon" type="image/png" sizes="<?php echo $size . 'x' . $size ?>" href="<?= $iconPath . 'favicon-'. $size . 'x' . $size . '.png' ?>">
  <?php } ?>
  <?php foreach ($touchIcons as $size) { ?>
    <link rel="apple-touch-icon" sizes="<?php echo $size . 'x' . $size ?>" href="<?= $iconPath . 'apple-icon-'. $size . 'x' . $size . '.png' ?>">
  <?php } ?>
  <?php foreach ($androidIcons as $size) { ?>
    <link rel="icon" type="image/png" sizes="<?php echo $size . 'x' . $size ?>" href="<?= $iconPath . 'android-icon-'. $size . 'x' . $size . '.png' ?>">
  <?php } ?>
  <?php foreach ($msTiles as $size) { ?>
    <meta name="msapplication-square<?php echo $size . 'x' . $size ?>logo" content="<?= $iconPath . 'ms-icon-'. $size . 'x' . $size . '.png' ?>">
  <?php } ?>
  <?php // don't forget to make this one too, actual size is 558x270 ?>
  <!-- <meta name="msapplication-wide310x150logo" content="<?= $iconPath . 'favicon-310x150.png' ?>"> -->

  <meta name="theme-color" content="<?php echo $themeColor ?>">
  <meta name="msapplication-TileColor" content="<?php echo $themeColor ?>" />
  <meta name="application-name" content="<?php bloginfo( 'name' ) ?>" />
  <?php
}

function electricpulp_get_site_logo(){
	$tag = 'a';
	$atts = array('id="site-title"');
	if( is_front_page() ){
		$tag = 'h1';
	}else{
		$atts = array_merge($atts, array(
			'href="' . home_url() . '"',
			'rel="home"'
		));
	}
	return '<' . $tag . ' ' . implode(' ',$atts) . '><span class="sr-only">' . get_bloginfo( 'name' ) . '</span></' . $tag . '>';
}

/**
 * Prints HTML with meta information for the current post-date/time.
 */
function electricpulp_posted_on() {
  $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
  if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
    $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
  }

  $time_string = sprintf( $time_string,
    esc_attr( get_the_date( DATE_W3C ) ),
    esc_html( get_the_date() ),
    esc_attr( get_the_modified_date( DATE_W3C ) ),
    esc_html( get_the_modified_date() )
  );

  $posted_on = sprintf(
    /* translators: %s: post date. */
    esc_html_x( 'Posted on %s', 'post date', 'electricpulp' ),
    '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
  );

  echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

}

/**
 * Prints HTML with meta information for the current author.
 */
function electricpulp_posted_by() {
  $byline = sprintf(
    /* translators: %s: post author. */
    esc_html_x( 'by %s', 'post author', 'electricpulp' ),
    '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
  );

  echo '<span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

}

/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function electricpulp_entry_footer() {
  // Hide category and tag text for pages.
  if ( 'post' === get_post_type() ) {
    /* translators: used between list items, there is a space after the comma */
    $categories_list = get_the_category_list( esc_html__( ', ', 'electricpulp' ) );
    if ( $categories_list ) {
      /* translators: 1: list of categories. */
      printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'electricpulp' ) . '</span>', $categories_list ); // WPCS: XSS OK.
    }

    /* translators: used between list items, there is a space after the comma */
    $tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'electricpulp' ) );
    if ( $tags_list ) {
      /* translators: 1: list of tags. */
      printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'electricpulp' ) . '</span>', $tags_list ); // WPCS: XSS OK.
    }
  }

  if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
    echo '<span class="comments-link">';
    comments_popup_link(
      sprintf(
        wp_kses(
          /* translators: %s: post title */
          __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'electricpulp' ),
          array(
            'span' => array(
              'class' => array(),
            ),
          )
        ),
        get_the_title()
      )
    );
    echo '</span>';
  }

  edit_post_link(
    sprintf(
      wp_kses(
        /* translators: %s: Name of current post. Only visible to screen readers */
        __( 'Edit <span class="screen-reader-text">%s</span>', 'electricpulp' ),
        array(
          'span' => array(
            'class' => array(),
          ),
        )
      ),
      get_the_title()
    ),
    '<span class="edit-link">',
    '</span>'
  );
}

/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 */
function electricpulp_post_thumbnail() {
  if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
    return;
  }

  if ( is_singular() ) :
    ?>

    <div class="post-thumbnail">
      <?php the_post_thumbnail(); ?>
    </div><!-- .post-thumbnail -->

  <?php else : ?>

  <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
    <?php
    the_post_thumbnail( 'post-thumbnail', array(
      'alt' => the_title_attribute( array(
        'echo' => false,
      ) ),
    ) );
    ?>
  </a>

  <?php
  endif; // End is_singular().
}

/* not all things that look like a title actually shoudl be, so pass in an ACF group comprised of:
title_text = text field
title_tag = select field with the following choices
h1 : Heading 1
h2 : Heading 2
h3 : Heading 3
p : Paragraph
div : No Semantic Value
*/
function electricpulp_make_title($title, $microTitle = false){
	$class = $microTitle ? 'micro-title' : 'title';
	echo '<' . $title['title_tag']. ' class="' . $class . '">' . $title['title_text'] . '</' . $title['title_tag'] . '>';
}

/**
 * Displays an inlined asset, most likely an svg
 */
function electricpulp_get_inlined($asset){
	switch ($asset) {
		case 'logo':
			$output = '';
			break;
		case 'menu':
			$output = '';
			break;
		case 'caret':
			$output = '';
			break;
		case 'close':
			$output = '';
			break;
		case 'arrow':
			$output = '';
			break;
		case 'twitter':
			$output = '';
			break;
		case 'facebook':
			$output = '';
      break;
    case 'instagram':
      $output = '';
      break;
		case 'linkedin':
			$output = '';
			break;

		default:
			$output = '';
			break;
	}
	return $output;
}

/**
 * Displays an inlined asset with hidden supporting text
 */
function electricpulp_get_icon($asset, $text){
	return electricpulp_get_inlined($asset) . '<span class="sr-only">' . $text . '</span>';
}

/**
 * Generate Fallback Image
 * @param string $size
 * @param string $color
 * @return string
 *
 * Based on the provided size and color, or lack there of, return an image path for one of the fallback image varients
 */
function electricpulp_generate_fallback_image($size = 'default', $color = 'random')
{
    $sizesAvailable = [
        'hero',       // 1500 / 600
        'blog-intro',  // 750 / 450
    ];

    $colorsAvailable = [
        'green',
        'blue',
    ];

    if (!in_array($size, $sizesAvailable)) return 'invalid size';

    if (!in_array($color, $colorsAvailable) && $color !== "random") return 'invalid color';

    $setColor = ($color === "random") ? $colorsAvailable[array_rand($colorsAvailable)] : $color;

    $filePath = get_template_directory_uri() . '/assets/images/fallbacks/' . $size . '-' . $setColor . '.jpg';

    return $filePath;
}
