<?php
get_header();
?>
<header class="archive-header">
    <div class="container">
        <h1 class="archive-title"><?php the_archive_title() ?></h1>
    </div>
</header>
<?php
if (have_posts()) {  ?>
    <div class="container">
        <div class="article-grid">
            <?php
            while (have_posts()) {
                the_post();
                the_title();
            }
            ?>
        </div>
    </div>
    <?php
} else {
    get_template_part('parts/element/not-found', null, ['text' => 'Sorry, no articles found for this topic.']);
}
get_footer();
