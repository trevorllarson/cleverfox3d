<?php
get_header();
?>
    <header class="page-header">
        <h1>
            Search results for &ldquo;<?php echo esc_html(get_search_query()); ?>&rdquo;
        </h1>
        <form id="post-type-search" action="/">
            <input type="text" name="s" value="<?php echo esc_attr(get_search_query()) ?>" aria-label="search">
            <?php if (isset($_GET['post_type']) && $_GET['post_type'] !== '') { ?>
                <input type="hidden" name="post_type" value="<?php esc_attr($_GET['post_type']) ?>" />
            <?php } ?>
            <button>Go</button>
        </form>
    </header>
<div class="container">
    <?php
    if (have_posts()) {
        ?>
        <div class="article-grid">
            <?php
            while (have_posts()) {
                the_post();
                get_template_part('parts/' . get_post_type() . '/loop-item');
            }
            ?>
        </div>
        <?php get_template_part('parts/element/paging'); ?>
        <?php
    } else {
        get_template_part('parts/element/not-found', '', ['text' => 'Sorry, but nothing matched your search terms. Please try again with different keywords.']);
    }
    ?>
</div>
<?php
get_footer();
