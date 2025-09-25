</main>

<footer id="site-footer">
    <div class="container">
        <div class="layout">
            <div id="copyright">
                <?php if ($value = get_field('copyright_text', 'options')) { ?>
                    <?php echo esc_html(str_replace('{year}', date('Y'), $value)) ?>
                <?php } ?>
            </div>
            <nav class="footer-menu" aria-label="footer navigation">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'footer',
                        'container'      => false,
                    )
                );
                ?>
            </nav>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>

</html>
