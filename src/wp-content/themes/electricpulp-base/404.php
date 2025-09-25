<?php
get_header();
?>
<header class="page-header">
	<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'electricpulp' ); ?></h1>
</header>
<?php
get_template_part( 'template-parts/content', 'none' );
get_footer();
